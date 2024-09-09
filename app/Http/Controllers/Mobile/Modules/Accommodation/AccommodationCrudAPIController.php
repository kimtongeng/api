<?php

namespace App\Http\Controllers\Mobile\Modules\Accommodation;

use App\Enums\Types\AttributeStatus;
use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use App\Models\BusinessCategory;
use App\Models\BusinessAttribute;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessStatus;
use App\Models\BusinessBankAccount;
use App\Enums\Types\IsBusinessOwner;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\GalleryPhotoType;
use App\Enums\Types\BankAccountStatus;
use App\Models\District;
use App\Models\Province;

class AccommodationCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Check Validation
     *
     */
    public function checkValidation($data)
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
            'business_category_id' => 'required|exists:business_category,id',
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
            'province_id' => 'required|exists:province,id',
            'district_id' => 'required|exists:district,id',
            'description' => 'required',
            'rate_count' => 'required|numeric|min:1|max:5',
            'price' => 'required',
            'policy' => 'nullable',
            'business_attribute' => 'required',
            'status' => 'required',
            'business_attribute.*.attribute_id' => 'required|exists:attribute,id',
            //deleted_business_attribute
            'deleted_business_attribute.*.id' => !empty($data['id']) && !empty($data['deleted_business_attribute']) ? 'required|exists:business_attribute,id' : 'nullable',
            'business_bank_account' => 'required',
            'business_bank_account.*.account_id' => 'required|exists:bank_account,id',
            //deleted_business_bank_account
            'deleted_business_bank_account.*.id' => !empty($data['id']) && !empty($data['deleted_business_bank_account']) ? 'required|exists:business_bank_account,id' : 'nullable',
        ]);
    }

    /**
     * Add Accommodation
     *
     */
    public function addAccommodation (Request $request)
    {
        $this->checkValidation($request);

        DB::beginTransaction();

        $business = new Business();

        //Merge Value Some Request
        $request->merge([
            Business::CONTACT_ID => $request->input('current_user_id'),
            Business::COUNTRY_ID => Contact::find($request->input('current_user_id'))->{Contact::COUNTRY_ID},
            Business::BUSINESS_TYPE_ID => BusinessTypeEnum::getAccommodation(),
        ]);

        // Set Data
        $business->setData($request);

        //Save Data
        if($business->save()) {
            //Upload Logo
            if(!empty($request->input($business->{Business::IMAGE}))) {
                $image = StringHelper::uploadImage($request->input(Business::IMAGE), ImagePath::accommodationLogo);
                $business->{Business::IMAGE} = $image;
                $business->save();
            }
            $gallery_photo_array = [];
            // Upload Gallery
            if(!empty($request->input('gallery_photo'))) {
                foreach ($request->input('gallery_photo') as $key => $obj) {
                    $data = [
                        GalleryPhoto::TYPE => GalleryPhotoType::getAccommodationCover(),
                        GalleryPhoto::TYPE_ID => $business->{Business::ID},
                        GalleryPhoto::ORDER => $key + 1
                    ];

                    $gallery_photo = new GalleryPhoto();
                    $gallery_photo->setData($data);
                    if($gallery_photo->save()) {
                        //Upload Image
                        $image = StringHelper::uploadImage($obj['image'], ImagePath::accommodationCover);
                        $gallery_photo->{GalleryPhoto::IMAGE} = $image;
                        $gallery_photo->save();
                        $gallery_photo_array[] = $gallery_photo;
                    }
                }
            }

            $business_attribute_array = [];
            //Set Business Attribute
            if (!empty($request->input('business_attribute'))) {
                foreach ($request->input('business_attribute') as $obj) {
                    $business_attribute_data = [
                        BusinessAttribute::BUSINESS_ID => $business->{Business::ID},
                        BusinessAttribute::BUSINESS_TYPE_ID => $business->{Business::BUSINESS_TYPE_ID},
                        BusinessAttribute::ATTRIBUTE_ID => $obj['attribute_id'],
                        BusinessAttribute::CREATED_AT => Carbon::now()
                    ];
                    $business_attribute = new BusinessAttribute();
                    $business_attribute->setData($business_attribute_data);
                    $business_attribute->save();
                    $business_attribute_array[] = $business_attribute;
                }
            }

            $business_bank_account_array = [];
            //Set Business Bank Account
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
            if($contactOwner->{Contact::IS_HOTEL_OWNER} == IsBusinessOwner::getNo()){
                $contactOwner->{Contact::IS_HOTEL_OWNER} = IsBusinessOwner::getYes();
                $contactOwner->save();
            }

            DB::commit();

            $response = [
                'has_payment_method' => Contact::hasBankAccount($contactOwner->{Contact::ID}, $business->{Business::BUSINESS_TYPE_ID}),
                'accommodation' => [
                    'id' => $business->id,
                    'business_type_id' => $business->business_type_id,
                    'business_type_name' => BusinessType::find($business->business_type_id)->name,
                    'contact_id' => $business->contact_id,
                    'contact_name' => $business->contact_name,
                    'name' => $business->name,
                    'business_category_id' => $business->business_category_id,
                    'business_category_name' => BusinessCategory::find($business->business_category_id)->name,
                    'province_id' => $business->province_id,
                    'province_name' => Province::find($business->province_id)->optional_name,
                    'district_id' => $business->district_id,
                    'district_name' => District::find($business->district_id)->optional_name,
                    'image' => $business->image,
                    'latitude' => $business->latitude,
                    'longitude' => $business->longitude,
                    'address' => $business->address,
                    'price' => $business->price,
                    'view_count' => $business->view_count,
                    'rate_count' => $business->rate_count,
                    'status' => $business->status,
                    'show' => $business->show,
                    'created_at' => $business->created_at,
                    'discount_label' => $business->discount_label,
                    'description' => $business->description,
                    'policy' => $business->policy,
                    'count_business' => $business->count_business,
                    'is_favorite' => $business->is_favorite,
                    'gallery_photo' => $gallery_photo_array,
                    'business_attribute' => $business_attribute_array,
                    'business_bank_accounts' => $business_bank_account_array
                ]
            ];

            return $this->responseWithData($response);
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit Accommodation
     *
     */
    public function editAccommodation (Request $request)
    {
        // Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $business = Business::find($request->input(Business::ID));


        if (!empty($business)) {
            // Merge Some Value Request
            $request->merge([
                Business::CONTACT_ID => $request->input('current_user_id'),
                Business::COUNTRY_ID => Contact::find($request->input('current_user_id'))->{Contact::COUNTRY_ID},
                Business::BUSINESS_TYPE_ID => BusinessTypeEnum::getAccommodation(),
            ]);

            // Set Data
            $business->setData($request);

            //Save Data
            if ($business->save()) {
                // Update Logo
                $image = StringHelper::editImage(
                    $request->input(Business::IMAGE),
                    $request->input('old_image'),
                    ImagePath::accommodationLogo
                );
                $business->{Business::IMAGE} = $image;
                $business->save();

                // Upload Or Update Cover
                if (!empty($request->input('gallery_photo'))) {
                    foreach ($request->input('gallery_photo') as $key => $obj) {
                        $orderNumber = $key + 1;
                        if (empty($obj[GalleryPhoto::ID])) {
                            $data = [
                                GalleryPhoto::TYPE => GalleryPhotoType::getAccommodationCover(),
                                GalleryPhoto::TYPE_ID => $business->{Business::ID},
                                GalleryPhoto::ORDER => $orderNumber
                            ];

                            $gallery_photo =  new GalleryPhoto();

                            $gallery_photo->setData($data);
                            if ($gallery_photo->save()) {
                                //Upload Cover
                                $image = StringHelper::uploadImage($obj[GalleryPhoto::IMAGE], ImagePath::accommodationCover);
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
                            StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::accommodationCover);
                        }
                    }
                }

                // Insert Or Update Business Attribute
                if (!empty($request->input('business_attribute'))) {
                    foreach ($request->input('business_attribute') as $obj) {
                        $business_attribute_data = [
                            BusinessAttribute::BUSINESS_ID => $business->{Business::ID},
                            BusinessAttribute::BUSINESS_TYPE_ID => $business->{Business::BUSINESS_TYPE_ID},
                            BusinessAttribute::ATTRIBUTE_ID => $obj['attribute_id'],
                        ];
                        if (empty($obj[BusinessAttribute::ID])) {
                            $business_attribute = new BusinessAttribute();
                            $business_attribute[BusinessAttribute::CREATED_AT] = Carbon::now();
                        } else {
                            $business_attribute = BusinessAttribute::find($obj[BusinessAttribute::ID]);
                            $business_attribute[BusinessAttribute::UPDATED_AT] = Carbon::now();
                        }
                        $business_attribute->setData($business_attribute_data);
                        $business_attribute->save();
                    }
                }

                //Delete Business Attribute
                if (!empty($request->input('deleted_business_attribute'))) {
                    foreach ($request->input('deleted_business_attribute') as $obj) {
                        if(!empty($obj[BusinessAttribute::ID])) {
                            BusinessAttribute::find($obj[BusinessAttribute::ID])->delete();
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
     * Delete Accommodation
     *
     */
    public function deleteAccommodation (Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'id' => 'required|exists:business,id',
        ]);

        DB::beginTransaction();

        $business = Business::find($request->input(Business::ID));

        if ($business->delete()) {
            // Delete Logo
            StringHelper::deleteImage($business->{Business::IMAGE}, ImagePath::accommodationLogo);

            // Delete Gallery Photo
            $gallery_photo_list = GalleryPhoto::where(GalleryPhoto::TYPE, GalleryPhotoType::getAccommodationCover())
                ->where(GalleryPhoto::TYPE_ID, $business->{Business::ID})
                ->get();
            foreach($gallery_photo_list as $obj) {
                $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::accommodationCover);
                $gallery_photo->delete();
            }

            // Delete Business Attribute
            BusinessAttribute::where(BusinessAttribute::BUSINESS_ID, $business->{Business::ID})->delete();

            //Delete Business Bank Account
            BusinessBankAccount::where(BusinessBankAccount::BUSINESS_ID, $business->{Business::ID})->delete();

            //Check and set shop owner if delete
            $shopByContact = Business::where(Business::CONTACT_ID, $request->input('current_user_id'))
                ->where(Business::BUSINESS_TYPE_ID, BusinessTypeEnum::getAccommodation())
                ->get();
            if (count($shopByContact) == 0) {
                //Remove seller owner from contact
                $contact = Contact::find($request->input('current_user_id'));
                $contact->{Contact::IS_HOTEL_OWNER} = IsBusinessOwner::getNo();
                $contact->save();
            }
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get Accommodation Category List
     *
     */
    public function getAccommodationCategoryList(Request $request)
    {
        $this->validate($request, [
            'business_type_id' => 'required|exists:business_type,id'
        ]);

        $tableSize = $request->input('table_size');
        empty($tableSize) ? $tableSize = 10 : $tableSize;

        $filter = [
            'business_type_id' => $request->input('business_type_id'),
            'search' => $request->input('search')
        ];

        $data = BusinessCategory::lists($filter)->paginate($tableSize);

        return $this->responseWithPagination($data);
    }

    /**
     * Get Accommodation
     *
     */
    public function getMyAccommodation(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.current_user_id' => 'required|exists:contact,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = 'newest';

        $data = Business::listAccommodation($filter, $sort)
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
