<?php


namespace App\Http\Controllers\Mobile\Modules\Shop;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Business;
use App\Models\District;
use App\Models\Province;
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
use Illuminate\Support\Facades\Auth;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\GalleryPhotoType;
use App\Models\BusinessMultiCategory;
use App\Models\BusinessStaffWorkDays;
use App\Enums\Types\BankAccountStatus;

class ShopCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    /**
     * Check Validation
     *
     */
    private function checkValidation($data)
    {
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:business,id' : 'nullable',
            'current_user_id' => 'required|exists:contact,id',
            'business_type_id' => 'required|exists:business_type,id',
            'image' => 'required',
            'old_image' => !empty($data['id']) ? 'required' : 'nullable',
            //gallery_photo
            'gallery_photo' => 'required',
            'gallery_photo.*.image' => 'required',
            //deleted_gallery_photo
            'deleted_gallery_photo.*.id' => !empty($data['id']) && !empty($data['deleted_gallery_photo']) ? 'required|exists:gallery_photo,id' : 'nullable',
            'name' => 'required',
            'free_delivery' => 'required',
            'delivery_fee' => 'nullable',
            //business working day
            'business_work_days' => 'required',
            'business_work_days.*.day' => 'required',
            //delete_business_work_days
            'deleted_business_work_days.*.id' => !empty($data['id']) && !empty($data['deleted_business_work_days'])  ? 'required|exists:business_staff_workdays,id' : 'nullable',
            'open_24_hour' => 'required',
            'open_time' => 'nullable',
            'close_time' => 'nullable',
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
            'province_id' => 'nullable',
            'district_id' => 'nullable',
            'description' => 'required',
            'status' => 'required',
            'category_list' => 'required',
            'category_list.*.category_id' => 'required|exists:business_category,id',
            //deleted_category_list
            'deleted_category_list.*.id' => !empty($data['id']) && !empty($data['deleted_category_list']) ? 'required|exists:business_category,id' : 'nullable',
            'business_bank_account' => 'required',
            'business_bank_account.*.account_id' => 'required|exists:bank_account,id',
            //deleted_business_bank_account
            'deleted_business_bank_account.*.id' => !empty($data['id']) && !empty($data['deleted_business_bank_account']) ? 'required|exists:business_bank_account,id' : 'nullable',
        ]);
    }

    /**
     * Add Shop
     *
     */
    public function addShop(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $business = new Business();

        //Merge Value Some Request
        $request->merge([
            Business::CONTACT_ID => $request->input('current_user_id'),
            Business::COUNTRY_ID => Contact::find($request->input('current_user_id'))->{Contact::COUNTRY_ID},
        ]);

        //Set Data
        $business->setData($request);

        //Save Data
        if ($business->save()) {
            //Upload Logo
            if (!empty($request->input(Business::IMAGE))) {
                $image = StringHelper::uploadImage($request->input(Business::IMAGE), ImagePath::shopLogo);
                $business->{Business::IMAGE} = $image;
                $business->save();
            }

            //Upload Cover
            $gallery_photo_array = [];
            if (!empty($request->input('gallery_photo'))) {
                foreach ($request->input('gallery_photo') as $key => $obj) {
                    $data = [
                        GalleryPhoto::TYPE => GalleryPhotoType::getShopCover(),
                        GalleryPhoto::TYPE_ID => $business->{Business::ID},
                        GalleryPhoto::ORDER => $key + 1
                    ];

                    $gallery_photo = new GalleryPhoto();
                    $gallery_photo->setData($data);
                    if ($gallery_photo->save()) {
                        //Upload Image
                        $image = StringHelper::uploadImage($obj['image'], ImagePath::shopCover);
                        $gallery_photo->{GalleryPhoto::IMAGE} = $image;
                        $gallery_photo->save();
                        $gallery_photo_array[] = $gallery_photo;
                    }
                }
            }

            //Set Shop Multi Category
            $category_list_array = [];
            if (!empty($request->input('category_list'))) {
                foreach ($request->input('category_list') as $obj) {
                    $shop_multi_category_data = [
                        BusinessMultiCategory::BUSINESS_ID => $business->{Business::ID},
                        BusinessMultiCategory::BUSINESS_CATEGORY_ID => $obj['category_id'],
                        BusinessMultiCategory::CREATED_AT => Carbon::now()
                    ];
                    $business_multi_category = new BusinessMultiCategory();
                    $business_multi_category->setData($shop_multi_category_data);
                    $business_multi_category->save();
                    $category_list_array[] = $business_multi_category;
                }
            }

            //Set Business Staff Work Days
            $business_work_days_array = [];
            if (!empty($request->input('business_work_days'))) {
                foreach ($request->input('business_work_days') as $obj) {
                    $business_work_days_data = [
                        BusinessStaffWorkDays::BUSINESS_ID => $business->{Business::ID},
                        BusinessStaffWorkDays::CONTACT_ID => $business->{Business::CONTACT_ID},
                        BusinessStaffWorkDays::DAY => $obj['day'],
                        BusinessStaffWorkDays::CREATED_AT => Carbon::now()
                    ];
                    $business_work_days = new BusinessStaffWorkDays();
                    $business_work_days->setData($business_work_days_data);
                    $business_work_days->save();
                    $business_work_days_array[] = $business_work_days;
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

            //Set Contact to shop owner
            $contactOwner = Contact::find($business->{Business::CONTACT_ID});
            if ($contactOwner->{Contact::IS_SELLER} == IsBusinessOwner::getNo()) {
                $contactOwner->{Contact::IS_SELLER} = IsBusinessOwner::getYes();
                $contactOwner->save();
            }

            DB::commit();

            $response = [
                'has_payment_method' => Contact::hasBankAccount($contactOwner->{Contact::ID}, $business->{Business::BUSINESS_TYPE_ID}),
                'shop' => [
                    'id' => $business->id,
                    'business_type_id' => $business->business_type_id,
                    'contact_id' => $business->contact_id,
                    'name' => $business->name,
                    'image' => $business->image,
                    'latitude' => $business->latitude,
                    'longitude' => $business->longitude,
                    'address' => $business->address,
                    'province_id' => $business->province_id,
                    'province_name' => Province::find($business->province_id)->optional_name,
                    'district_id' => $business->district_id,
                    'district_name' => District::find($business->district_id)->optional_name,
                    'free_delivery' => $business->free_delivery,
                    'open_24_hour' => $business->open_24_hour,
                    'open_time' => $business->open_time,
                    'close_time' => $business->close_time,
                    'discount_label' => $business->discount_label,
                    'description' => $business->description,
                    'gallery_photo' => $gallery_photo_array,
                    'business_category' => $category_list_array,
                    'business_work_days' => $business_work_days_array,
                    'business_bank_account' => $business_bank_account_array
                ]
            ];
            return $this->responseWithData($response);
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit Shop
     *
     */
    public function editShop(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $business = Business::find($request->input(Business::ID));

        if (!empty($business)) {
            //Merge Value Some Request
            $request->merge([
                Business::CONTACT_ID => $business->{Business::CONTACT_ID},
                Business::COUNTRY_ID => Contact::find($business->{Business::CONTACT_ID})->{Contact::COUNTRY_ID},
            ]);

            //Set Data
            $business->setData($request);

            //Save Data
            if ($business->save()) {
                //Update Logo
                $image = StringHelper::editImage(
                    $request->input(Business::IMAGE),
                    $request->input('old_image'),
                    ImagePath::shopLogo
                );
                $business->{Business::IMAGE} = $image;
                $business->save();

                //Upload Or Update Cover
                if (!empty($request->input('gallery_photo'))) {
                    foreach ($request->input('gallery_photo') as $key => $obj) {
                        $orderNumber = $key + 1;
                        if (empty($obj[GalleryPhoto::ID])) {
                            $data = [
                                GalleryPhoto::TYPE => GalleryPhotoType::getShopCover(),
                                GalleryPhoto::TYPE_ID => $business->{Business::ID},
                                GalleryPhoto::ORDER => $orderNumber
                            ];

                            $gallery_photo = new GalleryPhoto();

                            $gallery_photo->setData($data);
                            if ($gallery_photo->save()) {
                                //Upload Cover
                                $image = StringHelper::uploadImage($obj[GalleryPhoto::IMAGE], ImagePath::shopCover);
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
                            StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::shopCover);
                        }
                    }
                }

                //Set Shop Multi Category
                if (!empty($request->input('category_list'))) {
                    //Delete All Shop Multi Category
                    BusinessMultiCategory::where(BusinessMultiCategory::BUSINESS_ID, $business->id)->delete();

                    foreach ($request->input('category_list') as $obj) {
                        $shop_multi_category_data = [
                            BusinessMultiCategory::BUSINESS_ID => $business->id,
                            BusinessMultiCategory::BUSINESS_CATEGORY_ID => $obj['category_id']
                        ];

                        $business_multi_category = new BusinessMultiCategory();
                        $business_multi_category->setData($shop_multi_category_data);
                        $business_multi_category->save();
                    }
                }

                //Insert Or Update Business Work Days
                if (!empty($request->input('business_work_days'))) {
                    foreach ($request->input('business_work_days') as $obj) {
                        $business_work_days_data = [
                            BusinessStaffWorkDays::BUSINESS_ID => $business->{Business::ID},
                            BusinessStaffWorkDays::CONTACT_ID => $business->{Business::CONTACT_ID},
                            BusinessStaffWorkDays::DAY => $obj['day'],
                            BusinessStaffWorkDays::CREATED_AT => Carbon::now()
                        ];
                        if (empty($obj[BusinessStaffWorkDays::ID])) {
                            $business_work_days = new BusinessStaffWorkDays();
                        } else {
                            $business_work_days = BusinessStaffWorkDays::find($obj[BusinessStaffWorkDays::ID]);
                            $business_work_days->delete();
                        }
                        $business_work_days->setData($business_work_days_data);
                        $business_work_days->save();
                    }
                }

                //Delete Business Staff Work Days
                if (!empty($request->input('deleted_business_work_days'))) {
                    foreach ($request->input('deleted_business_work_days') as $obj) {
                        if (!empty($obj[BusinessStaffWorkDays::ID])){
                            $business_work_days = BusinessStaffWorkDays::find($obj[BusinessStaffWorkDays::ID]);
                            $business_work_days->delete();
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
     * Delete Shop
     *
     */
    public function deleteShop(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'id' => 'required|exists:business,id',
        ]);

        DB::beginTransaction();

        $business = Business::find($request->input(Business::ID));

        if ($business->delete()) {
            //Delete Logo
            StringHelper::deleteImage($business->{Business::IMAGE}, ImagePath::shopLogo);

            //Delete Gallery Photo
            $gallery_photo_list = GalleryPhoto::where(GalleryPhoto::TYPE, GalleryPhotoType::getShopCover())
                ->where(GalleryPhoto::TYPE_ID, $business->{Business::ID})
                ->get();
            foreach ($gallery_photo_list as $obj) {
                $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::shopCover);
                $gallery_photo->delete();
            }

            //Delete Business Bank Account
            BusinessBankAccount::where(BusinessBankAccount::BUSINESS_ID, $business->{Business::ID})->delete();

            //Deleted Business Work Day
            BusinessStaffWorkDays::where(BusinessStaffWorkDays::BUSINESS_ID, $business->{Business::ID})->delete();

            //Check and set shop owner if delete all
            $shopByContact = Business::where(Business::CONTACT_ID, $request->input('current_user_id'))
                ->where(function ($query) {
                    $query->where(Business::BUSINESS_TYPE_ID, BusinessTypeEnum::getShopRetail())
                        ->orWhere(Business::BUSINESS_TYPE_ID, BusinessTypeEnum::getShopWholesale())
                        ->orWhere(Business::BUSINESS_TYPE_ID, BusinessTypeEnum::getRestaurant())
                        ->orWhere(Business::BUSINESS_TYPE_ID, BusinessTypeEnum::getShopLocalProduct())
                        ->orWhere(Business::BUSINESS_TYPE_ID, BusinessTypeEnum::getService())
                        ->orWhere(Business::BUSINESS_TYPE_ID, BusinessTypeEnum::getModernCommunity());
                })
                ->whereNull(Business::DELETED_AT)
                ->get();
            if (count($shopByContact) == 0) {
                //Remove seller owner from contact
                $contact = Contact::find($request->input('current_user_id'));
                $contact->{Contact::IS_SELLER} = IsBusinessOwner::getNo();
                $contact->save();
            }
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get My Shop
     *
     */
    public function getMyShop(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.current_user_id' => 'required|exists:contact,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = 'newest';

        $data = Business::listShop($filter, $sort)
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
