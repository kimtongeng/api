<?php

namespace App\Http\Controllers\Mobile\Modules\Authentication;

use Carbon\Carbon;
use App\Helpers\FCM;
use App\Models\Contact;
use App\Helpers\MekongSMS;
use App\Models\PrefixCode;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use App\Models\ContactDevice;
use App\Helpers\Utils\ErrorCode;
use App\Enums\Types\ContactStatus;
use App\Enums\Types\IsContactLogin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Enums\Types\ContactLoginType;

class AuthController extends Controller
{
    private $expiry_in = null;

    public function __construct()
    {
        $this->middleware('auth:mobile', ['except' => ['login', 'requestOTPCode', 'resendOTPCode', 'activateAccount']]);
        $this->expiry_in = Carbon::now()->addYears(15)->timestamp;
    }

    public function guard()
    {
        return Auth::guard('mobile');
    }

    /**
     * Register Or Login.
     *
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'login_type' => 'required',
            'device_id' => 'required'
        ]);

        $loginType = $request->input('login_type');
        if ($loginType == ContactLoginType::byPhone()) {
            return $this->loginWithPhone($request);
        } else if ($loginType == ContactLoginType::byGoogle()) {
            return $this->loginWithGoogle($request);
        } else if ($loginType == ContactLoginType::byFacebook()) {
            return $this->loginWithFacebook($request);
        } else if ($loginType == ContactLoginType::byAppleId()) {
            return $this->loginWithAppleId($request);
        } else {
            $errors = ErrorCode::INVALID;
            return $this->responseValidation($errors);
        }
    }

    /**
     * Request OTP Code
     */
    public function requestOTPCode(Request $request)
    {
        info(StringHelper::encrypt($request->input('phone')));
        $request->merge(['phone' => StringHelper::decrypt($request->input('phone'))]);

        $this->validate($request, [
            'phone' => 'required|string',
            'country_id' => 'nullable'
        ]);

        //ReFormat Phone with Country Code
        $phoneNumber = StringHelper::formatPhoneWithCountryCode($request->input('phone'));


        $six_digit_random_number = StringHelper::randomCode();
        $res = MekongSMS::sendVerificationCode($six_digit_random_number, $phoneNumber);
        if ($res['code'] == MekongSMS::statusSuccess()) {
            //Time Out of OPT Code
            $timeoutDuration = 120;

            return $this->responseWithData([
                'phone' => $phoneNumber,
                'code' => $six_digit_random_number,
                'time_out' => $timeoutDuration,
            ]);
        } else {
            $error = ErrorCode::MEKONG_SMS_FAILED;
            $error['ERROR_FROM_MEKONG'] = $res;
            return $this->responseValidation($res);
        }
    }

    /**
     * Resend OTP Code
     */
    public function resendOTPCode(Request $request)
    {
        info(StringHelper::encrypt($request->input('phone')));
        $request->merge(['phone' => StringHelper::decrypt($request->input('phone'))]);

        $this->validate($request, [
            'phone' => 'required|string',
            'country_id' => 'nullable'
        ]);

        //ReFormat Phone with Country Code
        $phoneNumber = StringHelper::formatPhoneWithCountryCode($request->input('phone'));

        $six_digit_random_number = StringHelper::randomCode();
        $res = MekongSMS::sendVerificationCode($six_digit_random_number, $phoneNumber);
        if ($res['code'] == MekongSMS::statusSuccess()) {
            //Time Out of OPT Code
            $timeoutDuration = 120;

            return $this->responseWithData([
                'phone' => $phoneNumber,
                'code' => $six_digit_random_number,
                'time_out' => $timeoutDuration,
            ]);
        } else {
            $error = ErrorCode::MEKONG_SMS_FAILED;
            $error['ERROR_FROM_MEKONG'] = $res;
            return $this->responseValidation($res);
        }
    }

    /**
     * Register Or Login with Phone
     */
    private function loginWithPhone($request)
    {
        info(StringHelper::encrypt($request->input('phone')));
        $request->merge(['phone' => StringHelper::decrypt($request->input('phone'))]);

        $this->validate($request, [
            'phone' => 'required|string',
            'country_id' => 'nullable'
        ]);

        //ReFormat Phone with Country Code
        $phoneFormat = StringHelper::formatPhoneWithCountryCode($request->input('phone'));
        $request->merge(['phone' => $phoneFormat]);

        /**
         * Check has been register, Check Activated
         */
        $userData = Contact::where(Contact::PHONE, $request->input(Contact::PHONE))->first();

        if (!empty($userData)) {

            if ($userData->isActivated()) {
                //Login Old User
                if (!$token = Auth::guard('mobile')->setTTL($this->expiry_in)->login($userData)) {
                    return $this->responseValidation(ErrorCode::LOGIN_FAIL);
                }
                return $this->getResponseData($token);
            } else {
                //Check Activated
                $errors = ErrorCode::ACCOUNT_NOT_VERIFIED;
                $errors['id'] = $userData->id;
                return $this->responseValidation($errors);
            }
        } else {
            //Register New User
            $contact = new Contact();

            $contact->code = PrefixCode::getAutoCode(Contact::TABLE_NAME, PrefixCode::CONTACT);
            $contact->country_id = $request->input('country_id');
            $contact->fullname = Contact::getRandomPhoneName();
            $contact->phone = $request->input('phone');
            $contact->status = ContactStatus::getActivated();

            if ($contact->save()) {
                $contact = Contact::find($contact->id);
                if (!$token = Auth::guard('mobile')->setTTL($this->expiry_in)->login($contact)) {
                    return $this->responseValidation(ErrorCode::REGISTER_FAIL);
                }

                return $this->getResponseData($token);
            }
        }
    }

    /**
     * Register Or Login with Google
     */
    private function loginWithGoogle(Request $request)
    {
        $request->merge(['google' => StringHelper::decrypt($request->input('google'))]);

        $this->validate($request, [
            'country_id' => 'nullable',
            'fullname' => 'required',
            'google' => 'required|string|email'
        ]);

        /**
         * Check has been register, Check Activated
         */
        $userData = Contact::where(Contact::GOOGLE, $request->input(Contact::GOOGLE))->first();

        if (!empty($userData)) {

            if ($userData->isActivated()) {
                //Login Old User
                if (!$token = Auth::guard('mobile')->setTTL($this->expiry_in)->login($userData)) {
                    return $this->responseValidation(ErrorCode::LOGIN_FAIL);
                }

                return $this->getResponseData($token);
            } else {
                //Check Activated
                $errors = ErrorCode::ACCOUNT_NOT_VERIFIED;
                $errors['id'] = $userData->id;
                return $this->responseValidation($errors);
            }
        } else {
            //Register New User
            $contact = new Contact();

            $contact->code = PrefixCode::getAutoCode(Contact::TABLE_NAME, PrefixCode::CONTACT);
            $contact->country_id = $request->input('country_id');
            $contact->fullname = $request->input('fullname');
            $contact->google = $request->input('google');
            $contact->status = ContactStatus::getActivated();

            if ($contact->save()) {
                $contact = Contact::find($contact->id);
                if (!$token = Auth::guard('mobile')->setTTL($this->expiry_in)->login($contact)) {
                    return $this->responseValidation(ErrorCode::REGISTER_FAIL);
                }

                return $this->getResponseData($token);
            }
        }
    }

    /**
     * Register Or Login with Facebook
     */
    private function loginWithFacebook(Request $request)
    {
        $request->merge(['social_id' => StringHelper::decrypt($request->input('social_id'))]);

        $this->validate($request, [
            'country_id' => 'nullable',
            'fullname' => 'required',
            'social_id' => 'required|string|max:255'
        ]);

        /**
         * Check has been register, Check Activated
         */
        $userData = Contact::where(Contact::SOCIAL_ID, $request->input(Contact::SOCIAL_ID))->first();

        if (!empty($userData)) {

            if ($userData->isActivated()) {
                //Login Old User
                if (!$token = Auth::guard('mobile')->setTTL($this->expiry_in)->login($userData)) {
                    return $this->responseValidation(ErrorCode::LOGIN_FAIL);
                }

                return $this->getResponseData($token);
            } else {
                //Check Activated
                $errors = ErrorCode::ACCOUNT_NOT_VERIFIED;
                $errors['id'] = $userData->id;
                return $this->responseValidation($errors);
            }
        } else {
            //Register New User
            $contact = new Contact();

            $contact->code = PrefixCode::getAutoCode(Contact::TABLE_NAME, PrefixCode::CONTACT);
            $contact->country_id = $request->input('country_id');
            $contact->fullname = $request->input('fullname');
            $contact->social_id = $request->input('social_id');
            $contact->status = ContactStatus::getActivated();

            if ($contact->save()) {
                $contact = Contact::find($contact->id);
                if (!$token = Auth::guard('mobile')->setTTL($this->expiry_in)->login($contact)) {
                    return $this->responseValidation(ErrorCode::REGISTER_FAIL);
                }

                return $this->getResponseData($token);
            }
        }
    }

    /**
     * Register Or Login with Apple ID
     */
    private function loginWithAppleId(Request $request)
    {
        $request->merge(['apple_id' => StringHelper::decrypt($request->input('apple_id'))]);

        $this->validate($request, [
            'country_id' => 'nullable',
            'fullname' => 'nullable',
            'apple_id' => 'required|string|max:255'
        ]);

        /**
         * Check has been register, Check Activated
         */
        $userData = Contact::where(Contact::APPLE_ID, $request->input(Contact::APPLE_ID))->first();

        if (!empty($userData)) {

            if ($userData->isActivated()) {
                //Login Old User
                if (!$token = Auth::guard('mobile')->setTTL($this->expiry_in)->login($userData)) {
                    return $this->responseValidation(ErrorCode::LOGIN_FAIL);
                }

                return $this->getResponseData($token);
            } else {
                //Check Activated
                $errors = ErrorCode::ACCOUNT_NOT_VERIFIED;
                $errors['id'] = $userData->id;
                return $this->responseValidation($errors);
            }
        } else {
            //Register New User
            $contact = new Contact();

            $fullname = trim($request->input('fullname'));
            if ($fullname == "") {
                $fullname = Contact::getRandomAppleIdName();
            }

            $contact->code = PrefixCode::getAutoCode(Contact::TABLE_NAME, PrefixCode::CONTACT);
            $contact->country_id = $request->input('country_id');
            $contact->fullname = $fullname;
            $contact->apple_id = $request->input('apple_id');
            $contact->status = ContactStatus::getActivated();

            if ($contact->save()) {
                $contact = Contact::find($contact->id);
                if (!$token = Auth::guard('mobile')->setTTL($this->expiry_in)->login($contact)) {
                    return $this->responseValidation(ErrorCode::REGISTER_FAIL);
                }

                return $this->getResponseData($token);
            }
        }
    }


    /**
     * FCM Topic Block
     */
    public static function updateIdToContactDevice($contactID, $device_id)
    {
        info('Mobile Device ID: ' . $device_id);
        //Update Id To Contact Device
        $contact_device = ContactDevice::where(ContactDevice::DEVICE_ID, $device_id)->first();
        if (!empty($contact_device)) {
            ContactDevice::where(ContactDevice::DEVICE_ID, $device_id)
                ->update([
                    ContactDevice::CONTACT_ID => $contactID,
                    ContactDevice::IS_LOGIN => IsContactLogin::getYes(),
                    ContactDevice::UPDATED_AT => Carbon::now()
                ]);
        }
    }

    public function addOrUpdateFCMToken(Request $request)
    {
        $this->validate($request, [
            'fcm_token' => 'required',
            'device_id' => 'required'
        ]);

        $accountID = Auth::guard('mobile')->user()->id;
        $device_id = !empty($request->input('device_id')) ? $request->input('device_id') : 'default';
        $fcmToken = $request->input('fcm_token');

        $checkHasContactDevice = ContactDevice::where(ContactDevice::DEVICE_ID, $device_id)->get();

        if (count($checkHasContactDevice) > 0) {
            ContactDevice::where(ContactDevice::DEVICE_ID, $device_id)
                ->update([
                    ContactDevice::CONTACT_ID => $accountID,
                    ContactDevice::FCM_TOKEN => $fcmToken,
                    ContactDevice::IS_LOGIN => IsContactLogin::getYes(),
                    ContactDevice::UPDATED_AT => Carbon::now()
                ]);
        } else {
            $contact_device_data = [
                ContactDevice::CONTACT_ID => $accountID,
                ContactDevice::DEVICE_ID => $device_id,
                ContactDevice::FCM_TOKEN => $fcmToken,
                ContactDevice::IS_LOGIN => IsContactLogin::getYes(),
                ContactDevice::CREATED_AT => Carbon::now(),
            ];

            $contact_device = new ContactDevice();
            $contact_device->setData($contact_device_data);
            $contact_device->save();
        }

        $registrationToken = $fcmToken;
        $TOPIC_ANNOUNCEMENT = env('TOPIC_ANNOUNCEMENT');

        $subscribe = FCM::subscribeToTopic($TOPIC_ANNOUNCEMENT, $registrationToken);

        info('Subscribe Topic: ' . $accountID);

        return $this->responseWithSuccess();
    }

    /**
     * End FCM Topic Block
     */

    /**
     * Logout App
     */
    public function logout(Request $request)
    {
        $device_id = !empty($request->device_id) ? $request->device_id : 'default';
        $contact_id = Auth::guard('mobile')->user()->id;

        $checkUpdate = ContactDevice::where(ContactDevice::CONTACT_ID, $contact_id)
            ->where(ContactDevice::DEVICE_ID, $device_id)
            ->update([
                ContactDevice::IS_LOGIN => IsContactLogin::getNo()
            ]);

        if ($checkUpdate) {

            Auth::guard('mobile')->logout();
        }

        return response()->json(['success' => 1, 'message' => 'Successfully logged out.'], 200);
    }

    /**
     * Activate Account
     */
    public function activateAccount(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:contact,id'
        ]);

        $contact = Contact::find($request->input('id'));
        if (!empty($contact)) {
            $contact->status = ContactStatus::getActivated();
            $contact->save();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Deactivate Account
     */
    public function deactivateAccount(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:contact,id'
        ]);

        $contact = Contact::find($request->input('id'));
        if (!empty($contact)) {
            $contact->status = ContactStatus::getNotActivate();
            $contact->save();

            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * Delete Account
     */
    public function deleteAccount(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:contact,id'
        ]);

        $contact = Contact::find($request->input('id'));
        if ($contact->delete()) {
            ContactDevice::where(ContactDevice::CONTACT_ID, $contact->id)->delete();
            return $this->responseWithSuccess();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }
    }

    /**
     * get user information
     *
     * @param Request $request
     * @return void
     */
    public function getCurrentUser(Request $request)
    {
        $filter = $request->input('filter');
        $user = Contact::getCurrentUser($filter);
        $response = [
            'data' => [
                'user' => $user,
            ],
            'success' => 1,
            'message' => 'Your action has been completed successfully.',
        ];
        if (!empty($token)) {
            $response['data']['token'] = $token;
            $response['data']['token_type'] = 'bearer';
            $response['data']['expires_in'] = $this->expiry_in;
        }

        return response()->json($response, 200);
    }

    /**
     * get response data
     *
     * @param Request $request
     * @return void
     */
    private function getResponseData($token = null)
    {
        $response = [
            'data' => [
                // 'user' => Auth::guard('mobile')->user()
                'user' => Contact::getCurrentUser()
            ],
            'success' => 1,
            'message' => 'Your action has been completed successfully.',
        ];
        if (!empty($token)) {
            $response['data']['token'] = $token;
            $response['data']['token_type'] = 'bearer';
            $response['data']['expires_in'] = $this->expiry_in;
        }
        return response()->json($response, 200);
    }
}
