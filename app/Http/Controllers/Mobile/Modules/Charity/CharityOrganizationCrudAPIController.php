<?php

namespace App\Http\Controllers\Mobile\Modules\Charity;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Business;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Models\RelatedDocument;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\BusinessStatus;
use App\Models\BusinessBankAccount;
use App\Http\Controllers\Controller;
use App\Enums\Types\BusinessTypeEnum;
use App\Enums\Types\DocumentTypeEnum;
use App\Enums\Types\GalleryPhotoType;
use App\Enums\Types\BankAccountStatus;

class CharityOrganizationCrudAPIController extends Controller
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
            'business_category_id' => 'required',
            //gallery_photo
            'gallery_photo' => 'required',
            'gallery_photo.*.image' => 'required',
            //deleted_gallery_photo
            'deleted_gallery_photo.*.id' => !empty($data['id']) && !empty($data['deleted_gallery_photo']) ? 'required|exists:gallery_photo,id' : 'nullable',
            'name' => 'required',
            'phone' => 'required',
            'website_link' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
            'description' => 'required',
            'video_link' => 'nullable',
            //related_document
            'related_document' => 'required',
            'related_document.*.image' => 'required',
            //deleted_related_document
            'deleted_related_document.*.id' => !empty($data['id']) && !empty($data['deleted_related_document']) ? 'required|exists:related_document,id' : 'nullable',
            'business_bank_account' => 'required',
            'business_bank_account.*.account_id' => 'required|exists:bank_account,id',
            //deleted_business_bank_account
            'deleted_business_bank_account.*.id' => !empty($data['id']) && !empty($data['deleted_business_bank_account']) ? 'required|exists:business_bank_account,id' : 'nullable',
        ]);
    }

    /**
     * Add Charity Organization
     *
     */
    public function addCharityOrganization(Request $request)
    {
        //Check Validation
        $this->checkValidation($request);
        info($request);
        DB::beginTransaction();

        $business = new Business();

        //Merge Value Some Request
        $request->merge([
            Business::CONTACT_ID => $request->input('current_user_id'),
            Business::COUNTRY_ID => Contact::find($request->input('current_user_id'))->{Contact::COUNTRY_ID},
            Business::BUSINESS_TYPE_ID => BusinessTypeEnum::getCharityOrganization(),
        ]);

        //Set Data
        $business->setData($request);
        $business->{Business::STATUS} = BusinessStatus::getPending();

        //Save Data
        if ($business->save()) {
            //Upload Logo
            if (!empty($request->input(Business::IMAGE))) {
                $image = StringHelper::uploadImage($request->input(Business::IMAGE), ImagePath::charityOrganizationLogo);
                $business->{Business::IMAGE} = $image;
                $business->save();
            }

            //Upload Gallery Photo
            if (!empty($request->input('gallery_photo'))) {
                foreach ($request->input('gallery_photo') as $key => $obj) {
                    $data = [
                        GalleryPhoto::TYPE => GalleryPhotoType::getCharityOrganization(),
                        GalleryPhoto::TYPE_ID => $business->{Business::ID},
                        GalleryPhoto::ORDER => $key + 1
                    ];

                    $gallery_photo = new GalleryPhoto();
                    $gallery_photo->setData($data);
                    if ($gallery_photo->save()) {
                        //Upload Image
                        $image = StringHelper::uploadImage($obj['image'], ImagePath::charityOrganizationGallery);
                        $gallery_photo->{GalleryPhoto::IMAGE} = $image;
                        $gallery_photo->save();
                    }
                }
            }

            //Upload Related Document
            if (!empty($request->input('related_document'))) {
                foreach ($request->input('related_document') as $key => $obj) {
                    $data = [
                        RelatedDocument::DOC_TYPE_ID => DocumentTypeEnum::getBusinessLicense(),
                        RelatedDocument::BUSINESS_ID => $business->{Business::ID},
                        RelatedDocument::ORDER => $key + 1,
                    ];

                    $related_document = new RelatedDocument();
                    $related_document->setData($data);
                    if ($related_document->save()) {
                        //Upload Image
                        $image = StringHelper::uploadImage($obj[RelatedDocument::IMAGE], ImagePath::charityRelatedDoc);
                        $related_document->{RelatedDocument::IMAGE} = $image;
                        $related_document->save();
                    }
                }
            }

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
                }
            }

            DB::commit();

            $response = ['has_payment_method' => Contact::hasBankAccount($business->{Business::CONTACT_ID}, $business->{Business::BUSINESS_TYPE_ID})];
            return $this->responseWithData($response);
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Edit Charity Organization
     *
     */
    public function editCharityOrganization(Request $request)
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
                Business::BUSINESS_TYPE_ID => $business->{Business::BUSINESS_TYPE_ID},
            ]);

            //Set Data
            $business->setData($request);

            //Save Data
            if ($business->save()) {
                //Update Logo
                $image = StringHelper::editImage(
                    $request->input(Business::IMAGE),
                    $request->input('old_image'),
                    ImagePath::charityOrganizationLogo
                );
                $business->{Business::IMAGE} = $image;
                $business->save();

                //Upload Or Update Gallery Photo
                if (!empty($request->input('gallery_photo'))) {
                    foreach ($request->input('gallery_photo') as $key => $obj) {
                        $orderNumber = $key + 1;
                        if (empty($obj[GalleryPhoto::ID])) {
                            $data = [
                                GalleryPhoto::TYPE => GalleryPhotoType::getCharityOrganization(),
                                GalleryPhoto::TYPE_ID => $business->{Business::ID},
                                GalleryPhoto::ORDER => $orderNumber
                            ];

                            $gallery_photo = new GalleryPhoto();

                            $gallery_photo->setData($data);
                            if ($gallery_photo->save()) {
                                //Upload Image
                                $image = StringHelper::uploadImage($obj[GalleryPhoto::IMAGE], ImagePath::charityOrganizationGallery);
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
                            StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::charityOrganizationGallery);
                        }
                    }
                }

                //Upload Or Update Related Document
                if (!empty($request->input('related_document'))) {
                    foreach ($request->input('related_document') as $key => $obj) {
                        $orderNumber = $key + 1;
                        if (empty($obj[RelatedDocument::ID])) {
                            $data = [
                                RelatedDocument::DOC_TYPE_ID => DocumentTypeEnum::getBusinessLicense(),
                                RelatedDocument::BUSINESS_ID => $business->{Business::ID},
                                RelatedDocument::ORDER => $orderNumber,
                            ];

                            $related_document = new RelatedDocument();
                            $related_document->setData($data);
                            if ($related_document->save()) {
                                //Upload Image
                                $image = StringHelper::uploadImage($obj[RelatedDocument::IMAGE], ImagePath::charityRelatedDoc);
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
                            StringHelper::deleteImage($related_document->{RelatedDocument::IMAGE}, ImagePath::charityRelatedDoc);
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
     * Delete Charity Organization
     *
     */
    public function deleteCharityOrganization(Request $request)
    {
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'id' => 'required|exists:business,id',
        ]);

        DB::beginTransaction();

        $business = Business::find($request->input(Business::ID));

        //Delete Logo
        StringHelper::deleteImage($business->{Business::IMAGE}, ImagePath::charityOrganizationLogo);

        //Delete Gallery Photo
        $gallery_photo_list = GalleryPhoto::where(GalleryPhoto::TYPE, GalleryPhotoType::getCharityOrganization())
            ->where(GalleryPhoto::TYPE_ID, $business->{Business::ID})
            ->get();
        foreach ($gallery_photo_list as $obj) {
            $gallery_photo = GalleryPhoto::find($obj[GalleryPhoto::ID]);
            StringHelper::deleteImage($gallery_photo->{GalleryPhoto::IMAGE}, ImagePath::charityOrganizationGallery);
            $gallery_photo->delete();
        }

        //Delete Related Document
        $related_document_list = RelatedDocument::where(RelatedDocument::DOC_TYPE_ID, DocumentTypeEnum::getBusinessLicense())
            ->where(RelatedDocument::BUSINESS_ID, $business->{Business::ID})
            ->get();
        foreach ($related_document_list as $obj) {
            $related_document = RelatedDocument::find($obj[RelatedDocument::ID]);
            StringHelper::deleteImage($related_document->{RelatedDocument::IMAGE}, ImagePath::charityRelatedDoc);
            $related_document->delete();
        }

        //Delete Business Bank Account
        BusinessBankAccount::where(BusinessBankAccount::BUSINESS_ID, $business->{Business::ID})->delete();

        $business->delete();

        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Get My Charity Organization
     *
     */
    public function getMyCharityOrganization(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.current_user_id' => 'required|exists:contact,id'
        ]);

        $tableSize = empty($request->input('table_size')) ? 10 : $request->input('table_size');

        $filter = $request->input('filter');
        $sort = 'newest';

        $data = Business::listCharityOrganization($filter, $sort)
            ->with([
                'relatedDocument' => function ($query) {
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


    //Get Charity Detail
    public function getCharityOrganizationDetail(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required',
            'filter.organization_id' => 'required|exists:business,id'
        ]);

        $filter = $request->input('filter');
        $sort = $request->input('sort');

        $data = Business::listCharityOrganization($filter, $sort)->first();

        return $this->responseWithData($data);
    }
}
