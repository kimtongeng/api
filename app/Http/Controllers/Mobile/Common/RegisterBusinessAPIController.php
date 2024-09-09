<?php


namespace App\Http\Controllers\Mobile\Common;

use Carbon\Carbon;
use App\Helpers\FCM;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Models\ContactDevice;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\IsContactLogin;
use App\Http\Controllers\Controller;
use App\Enums\Types\ContactRegisterType;
use App\Models\BusinessAgencyBankAccount;
use App\Enums\Types\BusinessAgencyTypeEnum;
use App\Enums\Types\BusinessTypeEnum;
use App\Models\ContactBusinessInfo;
use App\Models\GeneralSetting;

class RegisterBusinessAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //Become Agency Or Update Info
    public function becomeAgencyOrUpdateInfo(Request $request)
    {
        DB::beginTransaction();

        //Check Validation User ID and Name
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'first_name' => 'required',
            'last_name' => 'required'
        ]);

        //Get Current Contact By ID
        $contactAgency = Contact::find($request->input('current_user_id'));

        //Alias FullName and Assign phone to agency phone
        $fullname = trim($request->input('first_name')) . ' ' . trim($request->input('last_name'));
        $request->merge([
            Contact::FULLNAME => $fullname,
            Contact::AGENCY_PHONE => $request->input(Contact::PHONE)
        ]);

        //Check has Referral Code and Decrypt
        if (!empty($request->input(Contact::REFERRAL_AGENCY_ID))) {
            $referralCode = StringHelper::decrypt($request->input(Contact::REFERRAL_AGENCY_ID));
            $request->merge([Contact::REFERRAL_AGENCY_ID => $referralCode]);
        }

        //Check Validation
        $messages = [
            'referral_agency_id.required' => 'invalid referral code',
            'referral_agency_id.exists' => 'invalid referral code',
        ];
        $this->validate($request, [
            'referral_agency_id' => 'required|exists:contact,id',
            'fullname' => empty($contactAgency->{Contact::FULLNAME}) ? 'required|unique:contact,fullname,NULL,id,deleted_at,NULL' : 'required',
            'gender' => empty($contactAgency->{Contact::GENDER}) ? 'required' : 'nullable',
            'phone' => 'nullable',
            'id_card' => empty($request->input('passport_no')) ? 'required' : 'nullable',
            'id_card_image_front' => !empty($request->input('id_card')) ? 'required' : 'nullable',
            'id_card_image_back' => !empty($request->input('id_card')) ? 'required' : 'nullable',
            'passport_no' => empty($request->input('id_card')) ? 'required' : 'nullable',
            'business_agency_bank_account' => 'required',
            'business_agency_bank_account.*.account_id' => 'required|exists:bank_account,id',
            //deleted_business_agency_bank_account
            'deleted_business_agency_bank_account.*.id' => !empty($data['id']) && !empty($data['deleted_business_agency_bank_account']) ? 'required|exists:business_agency_bank_account,id' : 'nullable',
        ], $messages);

        //Set Data
        $contactAgency->setAgencyData($request);

        if ($contactAgency->save()) {
            $idCardImagePath = ImagePath::realEstateAgencyIDCard;
            if (!empty($request->input(Contact::ID_CARD))) {
                //Upload Or Edit Image ID Card Front
                $image = $request->input(Contact::ID_CARD_IMAGE_FRONT);
                $oldImage = $request->input('old_' . Contact::ID_CARD_IMAGE_FRONT);
                if (!empty($image)) {
                    $idCardImageFront = StringHelper::editImage($image, $oldImage, $idCardImagePath);
                    $contactAgency->{Contact::ID_CARD_IMAGE_FRONT} = $idCardImageFront;
                    $contactAgency->save();
                }

                //Upload Or Edit Image ID Card Back
                $image = $request->input(Contact::ID_CARD_IMAGE_BACK);
                $oldImage = $request->input('old_' . Contact::ID_CARD_IMAGE_BACK);
                if (!empty($image)) {
                    $idCardImageBack = StringHelper::editImage($image, $oldImage, $idCardImagePath);
                    $contactAgency->{Contact::ID_CARD_IMAGE_BACK} = $idCardImageBack;
                    $contactAgency->save();
                }
            } else {
                //Delete Image Front
                StringHelper::deleteImage($contactAgency->{Contact::ID_CARD_IMAGE_FRONT}, $idCardImagePath);
                $contactAgency->{Contact::ID_CARD_IMAGE_FRONT} = null;

                //Delete Image Back
                StringHelper::deleteImage($contactAgency->{Contact::ID_CARD_IMAGE_BACK}, $idCardImagePath);
                $contactAgency->{Contact::ID_CARD_IMAGE_BACK} = null;
                $contactAgency->save();
            }

            //Insert or Update Business Bank Account
            if (!empty($request->input('business_agency_bank_account'))) {
                foreach ($request->input('business_agency_bank_account') as $obj) {
                    $business_agency_bank_account_data = [
                        BusinessAgencyBankAccount::BANK_ACCOUNT_ID => $obj['account_id'],
                        BusinessAgencyBankAccount::CONTACT_ID => $contactAgency->{Contact::ID},
                        BusinessAgencyBankAccount::BUSINESS_AGENCY_TYPE_ID => BusinessAgencyTypeEnum::getPropertyAgency(),
                    ];
                    if (empty($obj[BusinessAgencyBankAccount::ID])) {
                        $business_agency_bank_account = new BusinessAgencyBankAccount();
                        $business_agency_bank_account[BusinessAgencyBankAccount::CREATED_AT] = Carbon::now();
                    } else {
                        $business_agency_bank_account = BusinessAgencyBankAccount::find($obj[BusinessAgencyBankAccount::ID]);
                        $business_agency_bank_account[BusinessAgencyBankAccount::UPDATED_AT] = Carbon::now();
                    }
                    $business_agency_bank_account->setData($business_agency_bank_account_data);
                    $business_agency_bank_account->save();
                }
            }

            //Deleted Business BankAccount
            if (!empty($request->input('deleted_business_agency_bank_account'))) {
                foreach ($request->input('deleted_business_agency_bank_account') as $obj) {
                    if (!empty($obj[BusinessAgencyBankAccount::ID])) {
                        BusinessAgencyBankAccount::find($obj[BusinessAgencyBankAccount::ID])->delete();
                    }
                }
            }

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    //Become Massage Or Update Info
    public function becomeMassagerOrUpdateInfo(Request $request)
    {
        DB::beginTransaction();

        //Check Validation User ID and Name
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'first_name' => 'required',
            'last_name' => 'required'
        ]);

        //Get Current Contact By ID
        $contactMassager = Contact::find($request->input('current_user_id'));

        //Alias FullName and Assign phone to agency phone
        $fullname = trim($request->input('first_name')) . ' ' . trim($request->input('last_name'));
        $request->merge([
            Contact::FULLNAME => $fullname,
            Contact::AGENCY_PHONE => $request->input(Contact::PHONE)
        ]);

        //Check Validation

        $this->validate($request, [
            'fullname' => empty($contactMassager->{Contact::FULLNAME}) ? 'required|unique:contact,fullname,NULL,id,deleted_at,NULL' : 'required',
            'gender' => empty($contactMassager->{Contact::GENDER}) ? 'required' : 'required',
            'phone' => 'required',
            'profile_image' => 'required',
            'is_massager' => 'required',
        ]);

        //Set Data
        $contactMassager->setMassagerData($request);

        if ($contactMassager->save()) {
            //Upload Or Edit Image Profile Image
            if(!empty($request->input(Contact::PROFILE_IMAGE))) {
                $image = $request->input(Contact::PROFILE_IMAGE);
                $oldImage = $request->input('old_' . Contact::PROFILE_IMAGE);
                if (!empty($image)) {
                    $idCardImageFront = StringHelper::editImage($image, $oldImage, ImagePath::contactImagePath);
                    $contactMassager->{Contact::PROFILE_IMAGE} = $idCardImageFront;
                    $contactMassager->save();
                }
            } else {
                //Delete Image Front
                StringHelper::deleteImage($contactMassager->{Contact::PROFILE_IMAGE}, ImagePath::contactImagePath);
                $contactMassager->{Contact::PROFILE_IMAGE} = null;
                $contactMassager->save();
            }

            DB::commit();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    //Become Recipient Or Update Info
    public function becomeRecipientOrUpdateInfo(Request $request)
    {
        DB::beginTransaction();

        //Check Validation User ID and Name
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'first_name' => 'required',
            'last_name' => 'required'
        ]);

        //Get Current Contact By ID
        $contactNews = Contact::find($request->input('current_user_id'));

        //Alias FullName and Assign phone to agency phone
        $fullname = trim($request->input('first_name')) . ' ' . trim($request->input('last_name'));
        $request->merge([
            Contact::FULLNAME => $fullname,
        ]);

        //Check Validation
        $this->validate($request, [
            'fullname' => empty($contactNews->{Contact::FULLNAME}) ? 'required|unique:contact,fullname,NULL,id,deleted_at,NULL' : 'required',
            'phone' => 'required',
            'image' => 'nullable',
            'security_code' => 'nullable',
            'province_id' => 'required',
            'district_id' => 'required',
            'position_group_id' => 'required',
            'id_card' => 'required',
            'id_card_image_front' => !empty($request->input('id_card')) ? 'required' : 'nullable',
            'id_card_image_back' => !empty($request->input('id_card')) ? 'required' : 'nullable',
        ]);

        //set contact news
        $contactNews->setRecipientData($request);

        if ($contactNews->save()) {
            // compare security code
            $security_code = GeneralSetting::getSecurityCode();
            $code = $request->input('security_code');

            if (empty($contactNews->{Contact::POSITION_GROUP_ID})) {
                if ($security_code != $code) {
                    return $this->responseValidation(ErrorCode::ACTION_FAILED);
                }
            }

            //Find Contact Business Info
            $contactBusiness = ContactBusinessInfo::where('contact_id', $request->input('current_user_id'))
                ->where('contact_business_info.business_type_id', BusinessTypeEnum::getNews())
            ->first();

            if (empty($contactBusiness)) {
                $contactBusinessInfo = new ContactBusinessInfo();
            } else {
                $contactBusinessInfo = $contactBusiness;
            }

            // Set Contact Business Info data
            $contactBusinessInfoData = [
                ContactBusinessInfo::CONTACT_ID => $request->input('current_user_id'),
                ContactBusinessInfo::BUSINESS_TYPE_ID => BusinessTypeEnum::getNews(),
                ContactBusinessInfo::PHONE => $request->input('phone'),
            ];

            $contactBusinessInfo->setData($contactBusinessInfoData);

            if ($contactBusinessInfo->save()) {
                //Upload Or Edit Image
                if (!empty($request->input(ContactBusinessInfo::IMAGE))) {
                    $image = $request->input(ContactBusinessInfo::IMAGE);
                    $oldImage = $request->input('old_' . ContactBusinessInfo::IMAGE);
                    if (!empty($image)) {
                        $image = StringHelper::editImage($image, $oldImage, ImagePath::contactBusinessInfoImagePath);
                        $contactBusinessInfo->{ContactBusinessInfo::IMAGE} = $image;
                        $contactBusinessInfo->save();
                    }
                } else {
                    //Delete Image
                    StringHelper::deleteImage($request->input('old_image'), ImagePath::contactBusinessInfoImagePath);
                    $contactBusinessInfo->{ContactBusinessInfo::IMAGE} = null;
                    $contactBusinessInfo->save();
                }
            }

            $idCardImagePath = ImagePath::realEstateAgencyIDCard;
            if (!empty($request->input(Contact::ID_CARD))) {
                //Upload Or Edit Image ID Card Front
                $image = $request->input(Contact::ID_CARD_IMAGE_FRONT);
                $oldImage = $request->input('old_' . Contact::ID_CARD_IMAGE_FRONT);
                if (!empty($image)) {
                    $idCardImageFront = StringHelper::editImage($image, $oldImage, $idCardImagePath);
                    $contactNews->{Contact::ID_CARD_IMAGE_FRONT} = $idCardImageFront;
                    $contactNews->save();
                }

                //Upload Or Edit Image ID Card Back
                $image = $request->input(Contact::ID_CARD_IMAGE_BACK);
                $oldImage = $request->input('old_' . Contact::ID_CARD_IMAGE_BACK);
                if (!empty($image)) {
                    $idCardImageBack = StringHelper::editImage($image, $oldImage, $idCardImagePath);
                    $contactNews->{Contact::ID_CARD_IMAGE_BACK} = $idCardImageBack;
                    $contactNews->save();
                }
            } else {
                //Delete Image Front
                StringHelper::deleteImage($contactNews->{Contact::ID_CARD_IMAGE_FRONT}, $idCardImagePath);
                $contactNews->{Contact::ID_CARD_IMAGE_FRONT} = null;

                //Delete Image Back
                StringHelper::deleteImage($contactNews->{Contact::ID_CARD_IMAGE_BACK}, $idCardImagePath);
                $contactNews->{Contact::ID_CARD_IMAGE_BACK} = null;
                $contactNews->save();
            }

            DB::commit();
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    //Become Ktv girl Or Update info
    public function becomeKtvGirlOrUpdateInfo(Request $request)
    {
        //Check Validation User ID and Name
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'first_name' => 'required',
            'last_name' => 'required'
        ]);

        //Get Current Contact By ID
        $contactKtvGirl = Contact::find($request->input('current_user_id'));
        //Alias FullName and Assign phone to agency phone
        $fullname = trim($request->input('first_name')) . ' ' . trim($request->input('last_name'));
        $request->merge([
            Contact::FULLNAME => $fullname,
        ]);

        //Check Validation
        $this->validate($request, [
            'fullname' => empty($contactKtvGirl->{Contact::FULLNAME}) ? 'required|unique:contact,fullname,NULL,id,deleted_at,NULL' : 'required',
            'image' => 'nullable',
            'gender' => 'required',
            'phone' => 'required',
            'is_ktv_girl' => 'required|min:0|max:1',
        ]);

        //Set Ktv Girl Data
        $contactKtvGirl->setKtvGirlData($request);

        if($contactKtvGirl->save()) {
            //Find Contact Business Info
            $contactBusiness = ContactBusinessInfo::where('contact_id', $request->input('current_user_id'))
            ->where('contact_business_info.business_type_id', BusinessTypeEnum::getKtv())
            ->first();

            if (empty($contactBusiness)) {
                $contactBusinessInfo = new ContactBusinessInfo();
            } else {
                $contactBusinessInfo = $contactBusiness;
            }

            // Set Contact Business Info data
            $contactBusinessInfoData = [
                ContactBusinessInfo::CONTACT_ID => $request->input('current_user_id'),
                ContactBusinessInfo::BUSINESS_TYPE_ID => BusinessTypeEnum::getKtv(),
                ContactBusinessInfo::PHONE => $request->input('phone'),
            ];

            $contactBusinessInfo->setData($contactBusinessInfoData);

            if($contactBusinessInfo->save()) {
                //Upload Or Edit Image
                if (!empty($request->input(ContactBusinessInfo::IMAGE))) {
                    $image = $request->input(ContactBusinessInfo::IMAGE);
                    $oldImage = $request->input('old_' . ContactBusinessInfo::IMAGE);
                    if (!empty($image)) {
                        $image = StringHelper::editImage($image, $oldImage, ImagePath::contactBusinessInfoImagePath);
                        $contactBusinessInfo->{ContactBusinessInfo::IMAGE} = $image;
                        $contactBusinessInfo->save();
                    }
                } else {
                    //Delete Image
                    StringHelper::deleteImage($contactBusinessInfo->{ContactBusinessInfo::IMAGE}, ImagePath::contactBusinessInfoImagePath);
                    $contactBusinessInfo->{ContactBusinessInfo::IMAGE} = null;
                    $contactBusinessInfo->save();
                }
            }

            DB::commit();
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    //Become Driver Or Update info
    public function becomeDriverOrUpdateInfo(Request $request)
    {
        //Check Validation User ID and Name
        $this->validate($request, [
            'current_user_id' => 'required|exists:contact,id',
            'first_name' => 'required',
            'last_name' => 'required'
        ]);

        //Get Current Contact By ID
        $contactDriver = Contact::find($request->input('current_user_id'));
        //Alias FullName and Assign phone to agency phone
        $fullname = trim($request->input('first_name')) . ' ' . trim($request->input('last_name'));
        $request->merge([
            Contact::FULLNAME => $fullname,
        ]);

        //Check Validation
        $this->validate($request, [
            'fullname' => empty($contactDriver->{Contact::FULLNAME}) ? 'required|unique:contact,fullname,NULL,id,deleted_at,NULL' : 'required',
            'image' => 'nullable',
            'phone' => 'required',
            'id_card' => 'required',
            'id_card_image_front' => !empty($request->input('id_card')) ? 'required' : 'nullable',
            'id_card_image_back' => !empty($request->input('id_card')) ? 'required' : 'nullable',
            'vehicle_type_id' => 'required',
            'is_driver' => 'required|min:0|max:1',
        ]);

        $contactDriver->setDriverData($request);

        if ($contactDriver->save()) {
            //Find Contact Business Info
            $contactBusiness = ContactBusinessInfo::where('contact_id', $request->input('current_user_id'))
                ->where('contact_business_info.business_type_id', BusinessTypeEnum::getDelivery())
                ->first();

            if (empty($contactBusiness)) {
                $contactBusinessInfo = new ContactBusinessInfo();
            } else {
                $contactBusinessInfo = $contactBusiness;
            }

            // Set Contact Business Info data
            $contactBusinessInfoData = [
                ContactBusinessInfo::CONTACT_ID => $request->input('current_user_id'),
                ContactBusinessInfo::BUSINESS_TYPE_ID => BusinessTypeEnum::getDelivery(),
                ContactBusinessInfo::PHONE => $request->input('phone'),
            ];

            $contactBusinessInfo->setData($contactBusinessInfoData);

            if ($contactBusinessInfo->save()) {
                //Upload Or Edit Image
                if (!empty($request->input(ContactBusinessInfo::IMAGE))) {
                    $image = $request->input(ContactBusinessInfo::IMAGE);
                    $oldImage = $request->input('old_' . ContactBusinessInfo::IMAGE);
                    if (!empty($image)) {
                        $image = StringHelper::editImage($image, $oldImage, ImagePath::contactBusinessInfoImagePath);
                        $contactBusinessInfo->{ContactBusinessInfo::IMAGE} = $image;
                        $contactBusinessInfo->save();
                    }
                } else {
                    //Delete Image
                    StringHelper::deleteImage($request->input('old_image'), ImagePath::contactBusinessInfoImagePath);
                    $contactBusinessInfo->{ContactBusinessInfo::IMAGE} = null;
                    $contactBusinessInfo->save();
                }
            }

            $idCardImagePath = ImagePath::realEstateAgencyIDCard;
            if (!empty($request->input(Contact::ID_CARD))) {
                //Upload Or Edit Image ID Card Front
                $image = $request->input(Contact::ID_CARD_IMAGE_FRONT);
                $oldImage = $request->input('old_' . Contact::ID_CARD_IMAGE_FRONT);
                if (!empty($image)) {
                    $idCardImageFront = StringHelper::editImage($image, $oldImage, $idCardImagePath);
                    $contactDriver->{Contact::ID_CARD_IMAGE_FRONT} = $idCardImageFront;
                    $contactDriver->save();
                }

                //Upload Or Edit Image ID Card Back
                $image = $request->input(Contact::ID_CARD_IMAGE_BACK);
                $oldImage = $request->input('old_' . Contact::ID_CARD_IMAGE_BACK);
                if (!empty($image)) {
                    $idCardImageBack = StringHelper::editImage($image, $oldImage, $idCardImagePath);
                    $contactDriver->{Contact::ID_CARD_IMAGE_BACK} = $idCardImageBack;
                    $contactDriver->save();
                }
            } else {
                //Delete Image Front
                StringHelper::deleteImage($contactDriver->{Contact::ID_CARD_IMAGE_FRONT}, $idCardImagePath);
                $contactDriver->{Contact::ID_CARD_IMAGE_FRONT} = null;

                //Delete Image Back
                StringHelper::deleteImage($contactDriver->{Contact::ID_CARD_IMAGE_BACK}, $idCardImagePath);
                $contactDriver->{Contact::ID_CARD_IMAGE_BACK} = null;
                $contactDriver->save();
            }

            DB::commit();
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }
}
