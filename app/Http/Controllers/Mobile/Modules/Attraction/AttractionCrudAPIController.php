<?php

namespace App\Http\Controllers\Mobile\Modules\Attraction;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Business;
use App\Models\Category;
use App\Models\District;
use App\Models\Province;
use App\Models\BusinessType;
use App\Models\GalleryPhoto;
use App\Models\PlaceContact;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Models\PlacePriceList;
use App\Models\PlaceVideoList;
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

class AttractionCrudAPIController extends Controller
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
            'image' => 'required',
            'old_image' => !empty($data['id']) ? 'required' : 'nullable',
            //gallery_photo
            'gallery_photo' => 'required',
            'gallery_photo.*.image' => 'required',
            //deleted_gallery_photo
            'deleted_gallery_photo.*.id' => !empty($data['id']) && !empty($data['deleted_gallery_photo']) ? 'required|exists:gallery_photo,id' : 'nullable',
            'name' => 'required',
            'province_id' => 'required|exists:province,id',
            'district_id' => 'required|exists:district,id',
            'price' => 'nullable',
            'discount_label' => 'nullable',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'address' => 'nullable',
            'description' => 'required',
            'status' => 'required',
            //place_social_contact
            'place_social_contact.*.type' => !empty($data['place_social_contact']) ? 'required' : 'nullable',
            'place_social_contact.*.value' => !empty($data['place_social_contact']) ? 'required' : 'nullable',
            //deleted_place_social_contact
            'deleted_place_social_contact.*.id' => !empty($data['id']) && !empty($data['deleted_place_social_contact']) ? 'required|exists:place_contact,id' : 'nullable',
            //place_video_list
            'place_video_list.*.link' => !empty($data['place_video_list']) ? 'required' : 'nullable',
            //deleted_place_video_list
            'deleted_place_video_list.*.id' => !empty($data['id']) && !empty($data['deleted_place_video_list']) ? 'required|exists:place_video_list,id' : 'nullable',
            'business_bank_account' => 'nullable',
            'business_bank_account.*.account_id' => !empty($data['business_bank_account']) ? 'required|exists:bank_account,id' : 'nullable',
            //deleted_business_bank_account
            'deleted_business_bank_account.*.id' => !empty($data['id']) && !empty($data['deleted_business_bank_account']) ? 'required|exists:business_bank_account,id' : 'nullable',
        ]);
    }

    /**
     * Add Attraction
     *
     */
    public function addAttraction(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $business = new Business();

        //Merge Value Some Request
        $request->merge([
            Business::CONTACT_ID => $request->input('current_user_id'),
            Business::COUNTRY_ID => Contact::find($request->input('current_user_id'))->{Contact::COUNTRY_ID},
            Business::BUSINESS_TYPE_ID => BusinessTypeEnum::getAttraction(),
        ]);

        //Set Data
        $business->setData($request);

        //Save Data
        if ($business->save()) {
            //Upload Thumbnail
            if (!empty($request->input(Business::IMAGE))) {
                $image = StringHelper::uploadImage($request->input(Business::IMAGE), ImagePath::attractionThumb);
                $business->{Business::IMAGE} = $image;
                $business->save();
            }

            $gallery_photo_array = [];
            //Upload Gallery Photo
            if (!empty($request->input('gallery_photo'))) {
                foreach ($request->input('gallery_photo') as $key => $obj) {
                    $data = [
                        GalleryPhoto::TYPE => GalleryPhotoType::getAttraction(),
                        GalleryPhoto::TYPE_ID => $business->{Business::ID},
                        GalleryPhoto::ORDER => $key + 1
                    ];

                    $gallery_photo = new GalleryPhoto();
                    $gallery_photo->setData($data);
                    if ($gallery_photo->save()) {
                        //Upload Image
                        $image = StringHelper::uploadImage($obj['image'], ImagePath::attractionGallery);
                        $gallery_photo->{GalleryPhoto::IMAGE} = $image;
                        $gallery_photo->save();
                        $gallery_photo_array[] = $gallery_photo;
                    }
                }
            }

            $place_contact_array = [];
            //Set Place Contact Data
            if (!empty($request->input('place_social_contact'))) {
                foreach ($request->input('place_social_contact') as $obj) {
                    $data = [
                        PlaceContact::BUSINESS_ID => $business->{Business::ID},
                        PlaceContact::TYPE => $obj[PlaceContact::TYPE],
                        PlaceContact::VALUE => $obj[PlaceContact::VALUE]
                    ];

                    $place_contact = new PlaceContact();
                    $place_contact->setData($data);
                    $place_contact->save();
                    $place_contact_array[] = $place_contact;
                }
            }

            $place_video_list_array = [];
            //Set Place Video Data
            if (!empty($request->input('place_video_list'))) {
                foreach ($request->input('place_video_list') as $obj) {
                    $data = [
                        PlaceVideoList::BUSINESS_ID => $business->{Business::ID},
                        PlaceVideoList::LINK => $obj[PlaceVideoList::LINK],
                    ];

                    $place_video_list = new PlaceVideoList();
                    $place_video_list->setData($data);
                    $place_video_list->save();
                    $place_video_list_array[] = $place_video_list;
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

            //Set Contact to shop owner
            $contactOwner = Contact::find($business->{Business::CONTACT_ID});
            if ($contactOwner->{Contact::IS_ATTRACTION_OWNER} == IsBusinessOwner::getNo()) {
                $contactOwner->{Contact::IS_ATTRACTION_OWNER} = IsBusinessOwner::getYes();
                $contactOwner->save();
            }

            DB::commit();
            $response = [
                'has_payment_method' => Contact::hasBankAccount($contactOwner->{Contact::ID}, $business->{Business::BUSINESS_TYPE_ID}),
                'attraction' => [
                    'id' => $business->id,
                    'business_type_id' => $business->business_type_id,
                    'contact_id' => $business->contact_id,
                    'name' => $business->name,
                    'province_id' => $business->province_id,
                    'province_name' => Province::getProvinceNameById($business->province_id),
                    'district_id' => $business->district_id,
                    'district_name' => District::getDistrictNameById($business->district_id),
                    'image' => $business->image,
                    'latitude' => $business->latitude,
                    'longitude' => $business->longitude,
                    'address' => $business->address,
                    'price' => $business->price,
                    'view_count' => $business->view_count,
                    'rate_count' => $business->rate_count,
                    'status' => $business->status,
                    'created_at' => $business->created_at,
                    'discount_label' => $business->discount_label,
                    'description' => $business->description,
                    'is_favorite' => $business->is_favorite,
                    'gallery_photo' => $gallery_photo_array,
                    'place_social_contact' => $place_contact_array,
                    'place_video_list' => $place_video_list_array,
                    'business_bank_account' => $business_bank_account_array
                ]
            ];
            return $this->responseWithData($response);
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit Attraction
     *
     */
    public function editAttraction(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $business = Business::find($request->input(Business::ID));

        if(!empty($business)) {
            //Merge Value Some Request
            $request->merge([
                Business::CONTACT_ID => $business->{Business::CONTACT_ID},
                Business::COUNTRY_ID => Contact::find($business->{Business::CONTACT_ID})->{Contact::COUNTRY_ID},
                Business::BUSINESS_TYPE_ID => $business->{Business::BUSINESS_TYPE_ID},
            ]);

            //Set Data
            $business->setData($request);

            //Save Data
            if ($business->save()) {
                //Update Thumbnail
                $image = StringHelper::editImage(
                    $request->input(Business::IMAGE),
                    $request->input('old_image'),
                    ImagePath::attractionThumb
                );
                $business->{Business::IMAGE} = $image;
                $business->save();

                $gallery_photo_array = [];
                //Upload Or Update Gallery Photo
                if (!empty($request->input('gallery_photo'))) {
                    foreach ($request->input('gallery_photo') as $key => $obj) {
                        $orderNumber = $key + 1;
                        if (empty($obj[GalleryPhoto::ID])) {
                            $data = [
                                GalleryPhoto::TYPE => GalleryPhotoType::getAttraction(),
                                GalleryPhoto::TYPE_ID => $business->{Business::ID},
                                GalleryPhoto::ORDER => $orderNumber
                            ];

                            $gallery_photo = new GalleryPhoto();

                            $gallery_photo->setData($data);
                            if ($gallery_photo->save()) {
                                //Upload Image
                                $image = StringHelper::uploadImage($obj[GalleryPhoto::IMAGE], ImagePath::attractionGallery);
                                $gallery_photo->{GalleryPhoto::IMAGE} = $image;
                                $gallery_photo->save();
                                $gallery_photo_array[] = $gallery_photo;
                            }
                        } else {
                            $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                            $gallery_photo->{GalleryPhoto::ORDER} = $orderNumber;
                            $gallery_photo->save();
                            $gallery_photo_array[] = $gallery_photo;
                        }
                    }
                }

                //Check have deleted Gallery Photo
                if (!empty($request->input('deleted_gallery_photo'))) {
                    foreach ($request['deleted_gallery_photo'] as $obj) {
                        if (!empty($obj[GalleryPhoto::ID])) {
                            $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                            $gallery_photo->delete();
                            StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::attractionGallery);
                        }
                    }
                }

                $place_contact_array = [];
                //Set Or Update Place Contact
                if (!empty($request->input('place_social_contact'))) {
                    foreach ($request->input('place_social_contact') as $obj) {
                        $data = [
                            PlaceContact::BUSINESS_ID => $business->{Business::ID},
                            PlaceContact::TYPE => $obj[PlaceContact::TYPE],
                            PlaceContact::VALUE => $obj[PlaceContact::VALUE]
                        ];

                        if (empty($obj[PlaceContact::ID])) {
                            $place_contact = new PlaceContact();
                        } else {
                            $place_contact = PlaceContact::find($obj[PlaceContact::ID]);
                        }
                        $place_contact->setData($data);
                        $place_contact->save();
                        $place_contact_array[] = $place_contact;
                    }
                }

                //Check have deleted Place Contact
                if (!empty($request->input('deleted_place_social_contact'))) {
                    foreach ($request['deleted_place_social_contact'] as $obj) {
                        if (!empty($obj[PlaceContact::ID])) {
                            $place_contact = PlaceContact::find($obj[PlaceContact::ID]);
                            $place_contact->delete();
                        }
                    }
                }

                $place_video_list_array = [];
                //Set Or Update Place Video List
                if (!empty($request->input('place_video_list'))) {
                    foreach ($request->input('place_video_list') as $obj) {
                        $data = [
                            PlaceVideoList::BUSINESS_ID => $business->{Business::ID},
                            PlaceVideoList::LINK => $obj[PlaceVideoList::LINK],
                        ];

                        if (empty($obj[PlaceVideoList::ID])) {
                            $place_video_list = new PlaceVideoList();
                        } else {
                            $place_video_list = PlaceVideoList::find($obj[PlaceVideoList::ID]);
                        }
                        $place_video_list->setData($data);
                        $place_video_list->save();
                        $place_video_list_array[] = $place_video_list;
                    }
                }

                //Check have deleted Place Video List
                if (!empty($request->input('deleted_place_video_list'))) {
                    foreach ($request['deleted_place_video_list'] as $obj) {
                        if (!empty($obj[PlaceVideoList::ID])) {
                            $place_contact = PlaceVideoList::find($obj[PlaceVideoList::ID]);
                            $place_contact->delete();
                        }
                    }
                }

                $business_bank_account_array = [];
                //Insert or Update Business Bank Account
                if (!empty($request->input('business_bank_account'))) {
                    foreach ($request->input('business_bank_account') as $obj) {
                        $business_bank_account_data = [
                                BusinessBankAccount::BUSINESS_TYPE_ID => $business->{Business::BUSINESS_TYPE_ID},
                                BusinessBankAccount::BANK_ACCOUNT_ID => $obj['account_id'],
                                BusinessBankAccount::BUSINESS_ID => $business->{Business::ID},
                                BusinessBankAccount::CONTACT_ID => $business->{Business::CONTACT_ID},
                            ];
                        if (empty($obj[$request->input('id')])) {
                            $business_bank_account = new BusinessBankAccount();
                            $business_bank_account[BusinessBankAccount::CREATED_AT] = Carbon::now();
                        } else {
                            $business_bank_account = BusinessBankAccount::find($obj[$request->input('id')]);
                            $business_bank_account[BusinessBankAccount::UPDATED_AT] = Carbon::now();
                        }
                        $business_bank_account->setData($business_bank_account_data);
                        $business_bank_account->save();
                        $business_bank_account_array[] = $business_bank_account;
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

                $response = ['has_payment_method' => Contact::hasBankAccount($business->{Business::CONTACT_ID}, $business->{Business::BUSINESS_TYPE_ID}),
                    'attraction' => [
                        'id' => $business->id,
                        'business_type_id' => $business->business_type_id,
                        'contact_id' => $business->contact_id,
                        'name' => $business->name,
                        'province_id' => $business->province_id,
                        'province_name' => Province::getProvinceNameById($business->province_id),
                        'district_id' => $business->district_id,
                        'district_name' => District::getDistrictNameById($business->district_id),
                        'image' => $business->image,
                        'latitude' => $business->latitude,
                        'longitude' => $business->longitude,
                        'address' => $business->address,
                        'price' => $business->price,
                        'view_count' => $business->view_count,
                        'rate_count' => $business->rate_count,
                        'status' => $business->status,
                        'created_at' => $business->created_at,
                        'discount_label' => $business->discount_label,
                        'description' => $business->description,
                        'is_favorite' => $business->is_favorite,
                        'gallery_photo' => $gallery_photo_array,
                        'place_social_contact' => $place_contact_array,
                        'place_video_list' => $place_video_list_array,
                        'business_bank_account' => $business_bank_account_array
                    ]
                ];
                return $this->responseWithData($response);
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }
        }else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }

    /**
     * Delete Attraction
     *
     */
    public function deleteAttraction(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'id' => 'required|exists:business,id',
        ]);

        DB::beginTransaction();

        $business = Business::find($request->input(Business::ID));

        //Delete Thumbnail
        StringHelper::deleteImage($business->{Business::IMAGE}, ImagePath::attractionThumb);

        //Delete Gallery Photo
        $gallery_photo_list = GalleryPhoto::where(GalleryPhoto::TYPE, GalleryPhotoType::getAttraction())
            ->where(GalleryPhoto::TYPE_ID, $business->{Business::ID})
            ->get();
        foreach ($gallery_photo_list as $obj) {
            $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
            StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::attractionGallery);
            $gallery_photo->delete();
        }

        if ($business->delete()) {
            //Delete Place Contact
            PlaceContact::where(PlaceContact::BUSINESS_ID, $business->{Business::ID})->delete();

            //Delete Place Video List
            PlaceVideoList::where(PlaceVideoList::BUSINESS_ID, $business->{Business::ID})->delete();

            //Delete Place Price List
            $place_price_list = PlacePriceList::where(PlacePriceList::BUSINESS_ID, $business->{Business::ID})->get();
            foreach ($place_price_list as $obj) {
                $place_price = PlacePriceList::find($obj[PlacePriceList::ID]);
                StringHelper::deleteImage($place_price->{PlacePriceList::IMAGE}, ImagePath::attractionPlaceList);
                $place_price->delete();
            }

            //Delete Place Price Category
            Category::where(Category::BUSINESS_ID, $business->{Business::ID})->delete();

            //Delete Business Bank Account
            BusinessBankAccount::where(BusinessBankAccount::BUSINESS_ID, $business->{Business::ID})->delete();

            //Check and set attraction owner if delete all
            $attractionByContact = Business::where(Business::CONTACT_ID, $request->input('current_user_id'))
            ->where(Business::BUSINESS_TYPE_ID, BusinessTypeEnum::getAttraction())
            ->whereNull(Business::DELETED_AT)
            ->get();
            if (count($attractionByContact) == 0) {
                //Remove attraction owner from contact
                $contact = Contact::find($request->input('current_user_id'));
                $contact->{Contact::IS_ATTRACTION_OWNER} = IsBusinessOwner::getNo();
                $contact->save();
            }
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get My Attraction
     *
     */
    public function getMyAttraction(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.current_user_id' => 'required|exists:contact,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');
//        $sort = $request->input('sort');
        $sort = 'newest';

        $data = Business::listAttraction($filter, $sort)
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
