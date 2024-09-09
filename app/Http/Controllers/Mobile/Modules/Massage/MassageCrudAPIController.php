<?php

namespace App\Http\Controllers\Mobile\Modules\Massage;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Business;
use App\Models\Province;
use App\Models\BusinessType;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessStatus;
use App\Models\BusinessBankAccount;
use App\Enums\Types\IsBusinessOwner;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\GalleryPhotoType;
use App\Enums\Types\BankAccountStatus;
use App\Models\Commune;
use App\Models\District;

class MassageCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    private function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:business,id' : 'nullable',
            'current_user_id' => 'required|exists:contact,id',
            'image' => 'required',
            'old_image' => !empty($data['id']) ? 'required' : 'nullable',
            //gallery_photo
            'gallery_photo' => 'required',
            'gallery_photo.*.image' => 'required',
            //deleted_gallery_photo
            'deleted_gallery_photo.*.id' => !empty($data['id']) && !empty($data['deleted_gallery_photo']) ? 'required|exists:gallery_photo,id' : 'nullable',
            'name' => 'required',
            'description' => 'required',
            'phone' => 'required',
            'discount_label' => 'nullable',
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
            'province_id' => 'required',
            'price' => 'required',
            'district_id' => 'required',
            'commune_id' => 'required',
            'open_time' => 'required',
            'close_time' => 'required',
            'status' => 'required',
            'business_bank_account' => 'required',
            'business_bank_account.*.account_id' => 'required|exists:bank_account,id',
            //deleted_business_bank_account
            'deleted_business_bank_account.*.id' => !empty($data['id']) && !empty($data['deleted_business_bank_account']) ? 'required|exists:business_bank_account,id' : 'nullable',
        ]);
    }

    /**
     * Add Massage Place
     *
     */
    public function addMassage(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        $business = new Business();

        //Merge Value Some Request
        $request->merge([
            Business::CONTACT_ID => $request->input('current_user_id'),
            Business::COUNTRY_ID => Contact::find($request->input('current_user_id'))->{Contact::COUNTRY_ID},
            Business::BUSINESS_TYPE_ID => BusinessTypeEnum::getMassage(),
        ]);

        // Set Data
        $business->setData($request);


        if ($business->save()) {
            //Upload Logo
            if (!empty($request->input($business->{Business::IMAGE}))) {
                $image = StringHelper::uploadImage($request->input(Business::IMAGE), ImagePath::massageLogo);
                $business->{Business::IMAGE} = $image;
                $business->save();
            }

            // Upload Gallery
            $gallery_photo_array = [];
            if (!empty($request->input('gallery_photo'))) {
                foreach ($request->input('gallery_photo') as $key => $obj) {
                    $data = [
                        GalleryPhoto::TYPE => GalleryPhotoType::getMassageCover(),
                        GalleryPhoto::TYPE_ID => $business->{Business::ID},
                        GalleryPhoto::ORDER => $key + 1
                    ];

                    $gallery_photo = new GalleryPhoto();
                    $gallery_photo->setData($data);
                    if ($gallery_photo->save()) {
                        //Upload Image
                        $image = StringHelper::uploadImage($obj['image'], ImagePath::massageCover);
                        $gallery_photo->{GalleryPhoto::IMAGE} = $image;
                        $gallery_photo->save();
                        $gallery_photo_array[] = $gallery_photo;
                    }
                }
            }

            //Set Business Bank Account
            $business_bank_account_array = [];
            if (!empty($request->input('business_bank_account'))) {
                foreach ($request->input('business_bank_account') as $obj) {
                    $business_bank_account_data = [
                        BusinessBankAccount::BUSINESS_TYPE_ID => $business->{Business::BUSINESS_TYPE_ID},
                        BusinessBankAccount::BANK_ACCOUNT_ID => $obj['account_id'],
                        BusinessBankAccount::BUSINESS_ID => $business->{Business::ID},
                        BusinessBankAccount::CONTACT_ID => $business->{Business::CONTACT_ID},
                        BusinessBankAccount::CREATED_AT => Carbon::now()
                    ];
                    $business_bank_account = new BusinessBankAccount();
                    $business_bank_account->setData($business_bank_account_data);
                    $business_bank_account->save();
                    $business_bank_account_array[] = $business_bank_account;
                }
            }

            // ContactOwner
            $contactOwner = Contact::find($business->{Business::CONTACT_ID});
            if ($contactOwner->{Contact::IS_MASSAGE_OWNER} == IsBusinessOwner::getNo()) {
                $contactOwner->{Contact::IS_MASSAGE_OWNER} = IsBusinessOwner::getYes();
                $contactOwner->save();
            }

            DB::commit();

            $response = [
                'has_payment_method' => Contact::hasBankAccount($contactOwner->{Contact::ID}, $business->{Business::BUSINESS_TYPE_ID}),
                'massage' => [
                    'id' => $business->id,
                    'business_type_id' => $business->business_type_id,
                    'business_type_name' => BusinessType::find($business->business_type_id)->name,
                    'contact_id' => $business->contact_id,
                    'contact_name' => $business->contact_name,
                    'name' => $business->name,
                    'province_id' => $business->province_id,
                    'province_name' => Province::find($business->province_id)->optional_name,
                    'district_id' => $business->district_id,
                    'district_name' => District::find($business->district_id)->optional_name,
                    'commune_id' => $business->commune_id,
                    'commune_name' => Commune::find($business->commune_id)->optional_name,
                    'phone' => $business->phone,
                    'description' => $business->description,
                    'image' => $business->image,
                    'latitude' => $business->latitude,
                    'longitude' => $business->longitude,
                    'address' => $business->address,
                    'discount_label' => $business->discount_label,
                    'open_time' => $business->open_time,
                    'close_time' => $business->close_time,
                    'view_count' => $business->{Business::VIEW_COUNT},
                    'rate_count' => $business->{Business::RATE_COUNT},
                    'status' => $business->{Business::STATUS},
                    'created_at' => $business->created_at,
                    'gallery_photo' => $gallery_photo_array,
                    'business_bank_accounts' => $business_bank_account_array
                ]
            ];
            return $this->responseWithData($response);
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit Massage Place
     *
     */
    public function editMassage(Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        $business = Business::find($request->input(Business::ID));

        if (!empty($business)) {
            // Merge Some Value Request
            $request->merge([
                Business::CONTACT_ID => $request->input('current_user_id'),
                Business::COUNTRY_ID => Contact::find($request->input('current_user_id'))->{Contact::COUNTRY_ID},
                Business::BUSINESS_TYPE_ID => BusinessTypeEnum::getMassage(),
            ]);

            // Set Data
            $business->setData($request);

            //Save Data
            if ($business->save()) {
                // Update Logo
                $image = StringHelper::editImage(
                    $request->input(Business::IMAGE),
                    $request->input('old_image'),
                    ImagePath::massageLogo
                );
                $business->{Business::IMAGE} = $image;
                $business->save();

                //Upload or Update Gallery Photo
                if(!empty($request->input('gallery_photo'))) {
                    foreach($request->input('gallery_photo') as $key => $obj) {
                        $orderNumber = $key + 1;
                        if (empty($obj[GalleryPhoto::ID])) {
                            $data = [
                                GalleryPhoto::TYPE => GalleryPhotoType::getMassageCover(),
                                GalleryPhoto::TYPE_ID => $business->{Business::ID},
                                GalleryPhoto::ORDER => $orderNumber
                            ];

                            $gallery_photo =  new GalleryPhoto();

                            $gallery_photo->setData($data);
                            if ($gallery_photo->save()) {
                                //Upload Cover
                                $image = StringHelper::uploadImage($obj[GalleryPhoto::IMAGE], ImagePath::massageCover);
                                $gallery_photo->{GalleryPhoto::IMAGE} = $image;
                                $gallery_photo->save();
                            }
                        } else {
                            $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                            $gallery_photo->{GalleryPhoto::ORDER} = $orderNumber;
                            $gallery_photo->save();
                        }
                    }
                }

                //Check have deleted Cover
                if (!empty($request->input('deleted_gallery_photo'))) {
                    foreach ($request['deleted_gallery_photo'] as $obj) {
                        if (!empty($obj[GalleryPhoto::ID])) {
                            $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                            $gallery_photo->delete();
                            StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::massageCover);
                        }
                    }
                }

                //Insert or Update Business Bank Account
                if (!empty($request->input('business_bank_account'))) {
                    foreach ($request->input('business_bank_account') as $obj) {
                        $business_bank_account_data = [
                            BusinessBankAccount::BUSINESS_TYPE_ID => $business->{Business::BUSINESS_TYPE_ID},
                            BusinessBankAccount::BANK_ACCOUNT_ID => $obj['account_id'],
                            BusinessBankAccount::BUSINESS_ID => $business->{Business::ID},
                            BusinessBankAccount::CONTACT_ID => $business->{Business::CONTACT_ID},
                        ];
                        if (empty($obj[BusinessBankAccount::ID])) {
                            $business_bank_account = new BusinessBankAccount();
                            $business_bank_account[BusinessBankAccount::CREATED_AT] = Carbon::now();
                        } else {
                            $business_bank_account = BusinessBankAccount::find($obj[BusinessBankAccount::ID]);
                            $business_bank_account[BusinessBankAccount::UPDATED_AT] = Carbon::now();
                        }
                        $business_bank_account->setData($business_bank_account_data);
                        $business_bank_account->save();
                    }
                }

                //Deleted Business BankAccount
                if (!empty($request->input('deleted_business_bank_account'))) {
                    foreach ($request->input('deleted_business_bank_account') as $obj) {
                        if (!empty($obj[BusinessBankAccount::ID])) {
                            BusinessBankAccount::find($obj[BusinessBankAccount::ID])->delete();
                        }
                    }
                }

                DB::commit();

                $response = ['has_payment_method' => Contact::hasBankAccount($business->{Business::CONTACT_ID}, $business->{Business::BUSINESS_TYPE_ID})];
                return $this->responseWithData($response);
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }

    /**
     * Delete Massage Place
     *
     */
    public function deleteMassage(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'id' => 'required|exists:business,id',
        ]);

        DB::beginTransaction();

        $business = Business::find($request->input(Business::ID));

        if($business->delete()) {
            // Delete Logo
            StringHelper::deleteImage($business->{Business::IMAGE}, ImagePath::massageLogo);

            // Delete Gallery Photo
            $gallery_photo_list = GalleryPhoto::where(GalleryPhoto::TYPE, GalleryPhotoType::getMassageCover())
                ->where(GalleryPhoto::TYPE_ID, $business->{Business::ID})
                ->get();
            foreach ($gallery_photo_list as $obj) {
                $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::massageCover);
                $gallery_photo->delete();
            }

            //Delete Business Bank Account
            BusinessBankAccount::where(BusinessBankAccount::BUSINESS_ID, $business->{Business::ID})->delete();

            //Check and set shop owner if delete
            $shopByContact = Business::where(Business::CONTACT_ID, $request->input('current_user_id'))
            ->where(Business::BUSINESS_TYPE_ID, BusinessTypeEnum::getMassage())
            ->get();
            if (count($shopByContact) == 0) {
                //Remove seller owner from contact
                $contact = Contact::find($request->input('current_user_id'));
                $contact->{Contact::IS_MASSAGE_OWNER} = IsBusinessOwner::getNo();
                $contact->save();
            }
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get My Massage Place
     *
     */
    public function getMyMassage(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.current_user_id' => 'required|exists:contact,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = 'newest';

        $data = Business::listMassage($filter, $sort)
            ->with([
                'businessBankAccount' => function ($query) {
                    $query->select(
                        'business_bank_account.id',
                        'business_bank_account.business_id',
                        'bank.id as bank_id',
                        'bank.name as bank_name',
                        'bank.image as bank_image',
                        'business_bank_account.bank_account_id',
                        'bank_account.account_name',
                        'bank_account.account_number',
                        'bank_account.account_qr_code',
                    )
                        ->where('bank_account.status', BankAccountStatus::getEnabled())
                        ->orderBy('business_bank_account.id', 'DESC')
                        ->get();
                }
            ])
            ->paginate($tableSize);

        return $this->responseWithPagination($data);
    }
}
