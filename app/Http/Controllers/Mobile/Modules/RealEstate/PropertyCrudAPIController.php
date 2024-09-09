<?php


namespace App\Http\Controllers\Mobile\Modules\RealEstate;

use App\Enums\Types\BankAccountStatus;
use App\Enums\Types\BusinessStatus;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\ContactHasPermission;
use App\Enums\Types\ContactNotificationType;
use App\Enums\Types\DocumentTypeEnum;
use App\Enums\Types\GalleryPhotoType;
use App\Enums\Types\IsBusinessOwner;
use App\Enums\Types\PropertyTypeEnum;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use App\Models\BankAccount;
use App\Models\Business;
use App\Models\BusinessBankAccount;
use App\Models\BusinessPermission;
use App\Models\Contact;
use App\Models\GalleryPhoto;
use App\Models\Notification;
use App\Models\PrefixCode;
use App\Models\PropertyAsset;
use App\Models\RelatedDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyCrudAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile,check_user_is_property_owner');
    }

    /**
     * Check Validation
     *
     */
    private function checkValidation($data)
    {
        //Custom Messages Validations
        $messages = [
            'sale_assistance_id.required' => 'invalid sale assistance code',
            'sale_assistance_id.exists' => 'invalid sale assistance code',
        ];

        //Check Validation
        $this->validate($data, [
            'id' => !empty($data['id']) ? 'required|exists:business,id' : 'nullable',
            'current_user_id' => 'required|exists:contact,id',
            'property_type_id' => !empty($data['id']) ? 'required|exists:property_type,id' : 'nullable',
            'property_type' => !empty($data['id']) ? 'required' : 'nullable',
            'image' => 'nullable',
            'old_image' => 'nullable',
            //gallery_photo
            'gallery_photo' => 'required',
            'gallery_photo.*.image' => 'required',
            //deleted_gallery_photo
            'deleted_gallery_photo.*.id' => !empty($data['id']) && !empty($data['deleted_gallery_photo']) ? 'required|exists:gallery_photo,id' : 'nullable',
            'name' => 'required',
            'description' => 'required',
            'payment_policy' => 'required',
            'project_development' => 'required',
            'phone' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
            'province_id' => 'required',
            'district_id' => 'required',
            'commune_id' => 'required',
            'telegram_number' => 'nullable',
            'telegram_qr_code' => 'nullable',
            'sale_assistance_id' => !empty($data['sale_assistance_id']) ? 'required|exists:contact,id' : 'nullable',
            'sale_assistance_commission_type' => !empty($data['sale_assistance_commission']) ? 'required' : 'nullable',
            'agency_commission' => 'required',
            'agency_commission_type' => 'required',
            'ref_agency_commission' => 'required',
            'ref_agency_commission_type' => 'required',
            //id_card_image
            'id_card_image' => 'required',
            'id_card_image.*.image' => 'required',
            //deleted_id_card_image
            'deleted_id_card_image.*.id' => !empty($data['id']) && !empty($data['deleted_id_card_image']) ? 'required|exists:related_document,id' : 'nullable',
            //related_document
            'related_document' => 'required',
            'related_document.*.image' => 'required',
            //deleted_related_document
            'deleted_related_document.*.id' => !empty($data['id']) && !empty($data['deleted_related_document']) ? 'required|exists:related_document,id' : 'nullable',
            'business_bank_account' => 'required',
            'business_bank_account.*.account_id' => 'required|exists:bank_account,id',
            //deleted_business_bank_account
            'deleted_business_bank_account.*.id' => !empty($data['id']) && !empty($data['deleted_business_bank_account']) ? 'required|exists:business_bank_account,id' : 'nullable',
        ], $messages);
    }

    /**
     * Add Property
     *
     */
    public function addProperty(Request $request)
    {
        //Decrypt Sale Assistance ID
        if (!empty($request->input('sale_assistance_id'))) {
            $request->merge(['sale_assistance_id' => StringHelper::decrypt($request->input('sale_assistance_id'))]);
        }

        //Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $business = new Business();

        //Set Price
        $price = 0;
        if ($request->input('property_type') == PropertyTypeEnum::getSingle()) {
            $price = $request->input(Business::PRICE);
        }

        //Merge Value Some Request
        $request->merge([
            Business::CONTACT_ID => $request->input('current_user_id'),
            Business::COUNTRY_ID => Contact::find($request->input('current_user_id'))->{Contact::COUNTRY_ID},
            Business::BUSINESS_TYPE_ID => BusinessTypeEnum::getProperty(),
            Business::PRICE => $price
        ]);

        //Set Data
        $business->setData($request);
        $business->{Business::CODE} = PrefixCode::getAutoCode(Business::TABLE_NAME, PrefixCode::PROPERTY);
        $business->{Business::STATUS} = BusinessStatus::getApproved();
        $business->{Business::SALE_ASSISTANCE_ID} = $request->input('sale_assistance_id');

        //Save Data
        if ($business->save()) {
            //Upload Thumbnail
            if (!empty($request->input(Business::IMAGE))) {
                $image = StringHelper::uploadImage($request->input(Business::IMAGE), ImagePath::propertyThumb);
                $business->{Business::IMAGE} = $image;
                $business->save();
            }

            //Upload Gallery Photo
            if (!empty($request->input('gallery_photo'))) {
                foreach ($request->input('gallery_photo') as $key => $obj) {
                    $data = [
                        GalleryPhoto::TYPE => GalleryPhotoType::getProperty(),
                        GalleryPhoto::TYPE_ID => $business->{Business::ID},
                        GalleryPhoto::ORDER => $key + 1
                    ];

                    $gallery_photo = new GalleryPhoto();
                    $gallery_photo->setData($data);
                    if ($gallery_photo->save()) {
                        //Upload Image
                        $image = StringHelper::uploadImage($obj['image'], ImagePath::propertyGallery);
                        $gallery_photo->{GalleryPhoto::IMAGE} = $image;
                        $gallery_photo->save();
                    }
                }
            }

            //Upload ID Card Image
            if (!empty($request->input('id_card_image'))) {
                foreach ($request->input('id_card_image') as $key => $obj) {
                    $data = [
                        RelatedDocument::DOC_TYPE_ID => DocumentTypeEnum::getIDNo(),
                        RelatedDocument::BUSINESS_ID => $business->{Business::ID},
                        RelatedDocument::ORDER => $key + 1,
                    ];

                    $related_document = new RelatedDocument();
                    $related_document->setData($data);
                    if ($related_document->save()) {
                        //Upload Image
                        $image = StringHelper::uploadImage($obj[RelatedDocument::IMAGE], ImagePath::propertyIDCard);
                        $related_document->{RelatedDocument::IMAGE} = $image;
                        $related_document->save();
                    }
                }
            }

            //Upload Telegram Qr Code
            if (!empty($request->input(Business::TELEGRAM_QR_CODE))) {
                $image = StringHelper::uploadImage($request->input(Business::TELEGRAM_QR_CODE), ImagePath::propertyTelegram);
                $business->{Business::TELEGRAM_QR_CODE} = $image;
                $business->save();
            }

            //Upload Related Document
            if (!empty($request->input('related_document'))) {
                foreach ($request->input('related_document') as $key => $obj) {
                    $data = [
                        RelatedDocument::DOC_TYPE_ID => DocumentTypeEnum::getLandTitle(),
                        RelatedDocument::BUSINESS_ID => $business->{Business::ID},
                        RelatedDocument::ORDER => $key + 1,
                    ];

                    $related_document = new RelatedDocument();
                    $related_document->setData($data);
                    if ($related_document->save()) {
                        //Upload Image
                        $image = StringHelper::uploadImage($obj[RelatedDocument::IMAGE], ImagePath::propertyRelatedDoc);
                        $related_document->{RelatedDocument::IMAGE} = $image;
                        $related_document->save();
                    }
                }
            }

            //Set Business Bank Account
            if (!empty($request->input('business_bank_account'))) {
                foreach ($request->input('business_bank_account') as $obj) {
                    $business_bank_account_data = [
                        BusinessBankAccount::BUSINESS_TYPE_ID => BusinessTypeEnum::getProperty(),
                        BusinessBankAccount::BANK_ACCOUNT_ID => $obj['account_id'],
                        BusinessBankAccount::BUSINESS_ID => $business->{Business::ID},
                        BusinessBankAccount::CONTACT_ID => $business->{Business::CONTACT_ID},
                        BusinessBankAccount::CREATED_AT => Carbon::now()
                    ];
                    $business_bank_account = new BusinessBankAccount();
                    $business_bank_account->setData($business_bank_account_data);
                    $business_bank_account->save();
                }
            }

            //Set Contact to property owner
            $contactOwner = Contact::find($business->{Business::CONTACT_ID});
            if ($contactOwner->{Contact::IS_PROPERTY_OWNER} == IsBusinessOwner::getNo()) {
                $contactOwner->{Contact::IS_PROPERTY_OWNER} = IsBusinessOwner::getYes();
                $contactOwner->save();
            }

            //Set Contact to sale assistance
            if (!empty($business->{Business::SALE_ASSISTANCE_ID})) {
                $contactSaleAssistance = Contact::find($business->{Business::SALE_ASSISTANCE_ID});
                $contactSaleAssistance->{Contact::IS_SALE_ASSISTANCE} = IsBusinessOwner::getYes();

                //Send Notification
                if ($contactSaleAssistance->save()) {
                    $notificationData = [
                        'owner_name' => $contactOwner->{Contact::FULLNAME},
                        'name' => $business->{Business::NAME}
                    ];

                    $sendResponse = Notification::propertyNotification(
                        ContactNotificationType::getOwnerPropertyAddSaleAssistance(),
                        $business->{Business::SALE_ASSISTANCE_ID},
                        $business->{Business::ID},
                        $notificationData
                    );
                    info('Mobile Notification Owner Property Add Sale Assistance: ' . $sendResponse);
                }
            }

            DB::commit();

            $response = ['has_payment_method' => Contact::hasBankAccount($contactOwner->{Contact::ID}, BusinessTypeEnum::getProperty())];
            return $this->responseWithData($response);
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit Property
     *
     */
    public function editProperty(Request $request)
    {
        //Decrypt Sale Assistance ID
        if (!empty($request->input('sale_assistance_id'))) {
            $request->merge(['sale_assistance_id' => StringHelper::decrypt($request->input('sale_assistance_id'))]);
        }

        //Check Validation
        $this->checkValidation($request);

        DB::beginTransaction();

        $business = Business::find($request->input(Business::ID));
        $oldSaleAssistanceID = $business->{Business::SALE_ASSISTANCE_ID};

        //Set Price
        $price = 0;
        if ($request->input('property_type') == PropertyTypeEnum::getSingle()) {
            $price = $request->input(Business::PRICE);
        }

        //Merge Value Some Request
        $request->merge([
            Business::CONTACT_ID => $business->{Business::CONTACT_ID},
            Business::COUNTRY_ID => Contact::find($business->{Business::CONTACT_ID})->{Contact::COUNTRY_ID},
            Business::BUSINESS_TYPE_ID => $business->{Business::BUSINESS_TYPE_ID},
            Business::PRICE => $price
        ]);

        //Set Data
        $business->setData($request);
        $business->{Business::SALE_ASSISTANCE_ID} = $request->input('sale_assistance_id');

        //Save Data
        if ($business->save()) {
            //Update Thumbnail
            $image = StringHelper::editImage(
                $request->input(Business::IMAGE),
                $request->input('old_image'),
                ImagePath::propertyThumb
            );
            $business->{Business::IMAGE} = $image;
            $business->save();

            //Upload Or Update Gallery Photo
            if (!empty($request->input('gallery_photo'))) {
                foreach ($request->input('gallery_photo') as $key => $obj) {
                    $orderNumber = $key + 1;
                    if (empty($obj[GalleryPhoto::ID])) {
                        $data = [
                            GalleryPhoto::TYPE => GalleryPhotoType::getProperty(),
                            GalleryPhoto::TYPE_ID => $business->{Business::ID},
                            GalleryPhoto::ORDER => $orderNumber
                        ];

                        $gallery_photo = new GalleryPhoto();

                        $gallery_photo->setData($data);
                        if ($gallery_photo->save()) {
                            //Upload Image
                            $image = StringHelper::uploadImage($obj[GalleryPhoto::IMAGE], ImagePath::propertyGallery);
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

            //Check have deleted Gallery Photo
            if (!empty($request->input('deleted_gallery_photo'))) {
                foreach ($request['deleted_gallery_photo'] as $obj) {
                    if (!empty($obj[GalleryPhoto::ID])) {
                        $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                        $gallery_photo->delete();
                        StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::propertyGallery);
                    }
                }
            }

            //Upload Or Update ID Card Image
            if (!empty($request->input('id_card_image'))) {
                foreach ($request->input('id_card_image') as $key => $obj) {
                    $orderNumber = $key + 1;
                    if (empty($obj[RelatedDocument::ID])) {
                        $data = [
                            RelatedDocument::DOC_TYPE_ID => DocumentTypeEnum::getIDNo(),
                            RelatedDocument::BUSINESS_ID => $business->{Business::ID},
                            RelatedDocument::ORDER => $orderNumber,
                        ];

                        $related_document = new RelatedDocument();
                        $related_document->setData($data);
                        if ($related_document->save()) {
                            //Upload Image
                            $image = StringHelper::uploadImage($obj[RelatedDocument::IMAGE], ImagePath::propertyIDCard);
                            $related_document->{RelatedDocument::IMAGE} = $image;
                            $related_document->save();
                        }
                    } else {
                        $related_document = RelatedDocument::find($obj[RelatedDocument::ID]);
                        $related_document->{RelatedDocument::ORDER} = $orderNumber;
                        $related_document->save();
                    }
                }
            }

            //Check have deleted ID Card Image
            if (!empty($request->input('deleted_id_card_image'))) {
                foreach ($request['deleted_id_card_image'] as $obj) {
                    if (!empty($obj[RelatedDocument::ID])) {
                        $related_document = RelatedDocument::find($obj[RelatedDocument::ID]);
                        $related_document->delete();
                        StringHelper::deleteImage($related_document->{RelatedDocument::IMAGE}, ImagePath::propertyIDCard);
                    }
                }
            }

            //Update Telegram Qr Code
            $image = StringHelper::editImage(
                $request->input(Business::TELEGRAM_QR_CODE),
                $request->input('old_telegram_qr_code'),
                ImagePath::propertyTelegram
            );
            $business->{Business::TELEGRAM_QR_CODE} = $image;
            $business->save();

            //Upload Or Update Related Document
            if (!empty($request->input('related_document'))) {
                foreach ($request->input('related_document') as $key => $obj) {
                    $orderNumber = $key + 1;
                    if (empty($obj[RelatedDocument::ID])) {
                        $data = [
                            RelatedDocument::DOC_TYPE_ID => DocumentTypeEnum::getLandTitle(),
                            RelatedDocument::BUSINESS_ID => $business->{Business::ID},
                            RelatedDocument::ORDER => $orderNumber,
                        ];

                        $related_document = new RelatedDocument();
                        $related_document->setData($data);
                        if ($related_document->save()) {
                            //Upload Image
                            $image = StringHelper::uploadImage($obj[RelatedDocument::IMAGE], ImagePath::propertyRelatedDoc);
                            $related_document->{RelatedDocument::IMAGE} = $image;
                            $related_document->save();
                        }
                    } else {
                        $related_document = RelatedDocument::find($obj[RelatedDocument::ID]);
                        $related_document->{RelatedDocument::ORDER} = $orderNumber;
                        $related_document->save();
                    }
                }
            }

            //Check have deleted Related Document
            if (!empty($request->input('deleted_related_document'))) {
                foreach ($request['deleted_related_document'] as $obj) {
                    if (!empty($obj[RelatedDocument::ID])) {
                        $related_document = RelatedDocument::find($obj[RelatedDocument::ID]);
                        $related_document->delete();
                        StringHelper::deleteImage($related_document->{RelatedDocument::IMAGE}, ImagePath::propertyRelatedDoc);
                    }
                }
            }

            //Insert or Update Business Bank Account
            if (!empty($request->input('business_bank_account'))) {
                foreach ($request->input('business_bank_account') as $obj) {
                    $business_bank_account_data = [
                        BusinessBankAccount::BUSINESS_TYPE_ID => BusinessTypeEnum::getProperty(),
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

            //Set Contact to sale assistance
            if (
                !empty($business->{Business::SALE_ASSISTANCE_ID})
                && $business->{Business::SALE_ASSISTANCE_ID} != $oldSaleAssistanceID
            ) {
                $contactSaleAssistance = Contact::find($business->{Business::SALE_ASSISTANCE_ID});
                $contactSaleAssistance->{Contact::IS_SALE_ASSISTANCE} = IsBusinessOwner::getYes();

                //Send Notification
                if ($contactSaleAssistance->save()) {
                    $notificationData = [
                        'owner_name' => Contact::find($business->{Business::CONTACT_ID})->{Contact::FULLNAME},
                        'name' => $business->{Business::NAME}
                    ];

                    $sendResponse = Notification::propertyNotification(
                        ContactNotificationType::getOwnerPropertyAddSaleAssistance(),
                        $business->{Business::SALE_ASSISTANCE_ID},
                        $business->{Business::ID},
                        $notificationData
                    );
                    info('Mobile Notification Owner Property Add Sale Assistance: ' . $sendResponse);
                }
            }

            DB::commit();

            $response = ['has_payment_method' => Contact::hasBankAccount($business->{Business::CONTACT_ID}, BusinessTypeEnum::getProperty())];
            return $this->responseWithData($response);
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Delete Property
     *
     */
    public function deleteProperty(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'id' => 'required|exists:business,id',
        ]);

        DB::beginTransaction();

        $business = Business::find($request->input(Business::ID));

        if ($business->delete()) {
            //Delete Thumbnail
            StringHelper::deleteImage($business->{Business::IMAGE}, ImagePath::propertyThumb);

            //Delete Gallery Photo Single and Multi
            $gallery_photo_list = GalleryPhoto::where(function ($query) {
                $query->where(GalleryPhoto::TYPE, GalleryPhotoType::getProperty())
                    ->orWhere(GalleryPhoto::TYPE, GalleryPhotoType::getPropertyAsset());
            })
                ->where(GalleryPhoto::TYPE_ID, $business->{Business::ID})
                ->get();
            foreach ($gallery_photo_list as $obj) {
                $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
                StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::propertyGallery);
                $gallery_photo->delete();
            }

            //Delete ID Card Image
            $id_card_image_list = RelatedDocument::where(RelatedDocument::DOC_TYPE_ID, DocumentTypeEnum::getIDNo())
                ->where(RelatedDocument::BUSINESS_ID, $business->{Business::ID})
                ->get();
            foreach ($id_card_image_list as $obj) {
                $id_card_image = RelatedDocument::find($obj[RelatedDocument::ID]);
                StringHelper::deleteImage($id_card_image->{RelatedDocument::IMAGE}, ImagePath::propertyIDCard);
                $id_card_image->delete();
            }

            //Delete Telegram QR Code
            StringHelper::deleteImage($business->{Business::TELEGRAM_QR_CODE}, ImagePath::propertyTelegram);

            //Delete Related Document
            $related_document_list = RelatedDocument::where(RelatedDocument::DOC_TYPE_ID, DocumentTypeEnum::getLandTitle())
                ->where(RelatedDocument::BUSINESS_ID, $business->{Business::ID})
                ->get();
            foreach ($related_document_list as $obj) {
                $related_document = RelatedDocument::find($obj[RelatedDocument::ID]);
                StringHelper::deleteImage($related_document->{RelatedDocument::IMAGE}, ImagePath::propertyRelatedDoc);
                $related_document->delete();
            }


            //Delete Property Asset
            $property_asset_list = PropertyAsset::where(PropertyAsset::BUSINESS_ID, $business->{Business::ID})->get();
            foreach ($property_asset_list as $obj) {
                $property_asset = PropertyAsset::find($obj[PropertyAsset::ID]);
                StringHelper::deleteImage($property_asset->{PropertyAsset::IMAGE}, ImagePath::propertyThumb);
                $property_asset->delete();
            }

            //Delete Asset Category
            AssetCategory::where(AssetCategory::BUSINESS_ID, $business->{Business::ID})->delete();

            //Delete Business Bank Account
            BusinessBankAccount::where(BusinessBankAccount::BUSINESS_ID, $business->{Business::ID})->delete();

            //Check and set property owner if delete all
            $propertyByContact = Business::where(Business::CONTACT_ID, $request->input('current_user_id'))
                ->where(Business::BUSINESS_TYPE_ID, BusinessTypeEnum::getProperty())
                ->whereNull(Business::DELETED_AT)
                ->get();
            if (count($propertyByContact) == 0) {
                //Remove property owner from contact
                $contact = Contact::find($request->input('current_user_id'));
                $contact->{Contact::IS_PROPERTY_OWNER} = IsBusinessOwner::getNo();
                $contact->save();
            }
        }

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get My Property
     *
     */
    public function getMyProperty(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.current_user_id' => empty($request->input('filter.sale_assistance_id')) ? 'required|exists:contact,id' : 'nullable',
            'filter.sale_assistance_id' => empty($request->input('filter.current_user_id')) ? 'required|exists:contact,id' : 'nullable',
            'filter.property_type' => 'required'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = $request->input('sort');


        $data = Business::listProperty($filter, $sort)
            ->where('property_type.type', $filter['property_type'])
            ->with([
                'relatedDocument' => function ($query) {
                    $query->orderBy('related_document.order', 'ASC');
                },
                'idCardImage' => function ($query) {
                    $query->orderBy('related_document.order', 'ASC');
                },
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
