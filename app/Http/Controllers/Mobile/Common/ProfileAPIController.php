<?php


namespace App\Http\Controllers\Mobile\Common;

use App\Helpers\StringHelper;
use App\Helpers\Utils\ErrorCode;
use App\Helpers\Utils\ImagePath;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Post;
use App\Models\Support;
use Illuminate\Http\Request;

class ProfileAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile', ['except' => ['getPrivacyPolicy', 'getSupport']]);
    }

    /**
     * Update User Information
     */
    public function updateUserInfo(Request $request)
    {
        //Check Validation
        $this->validate($request, [
            'id' => 'required|exists:contact,id',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'gender' => 'required'
        ]);

        $contact = Contact::find($request->input(Contact::ID));
        if (!empty($contact)) {
            //Alias Full Name
            $fullName = trim($request->input('first_name')) . " " . trim($request->input('last_name'));
            $request->merge([Contact::FULLNAME => $fullName]);

            //ReFormat Phone with Country Code
            $phoneFormat = StringHelper::formatPhoneWithCountryCode($request->input(Contact::PHONE));
            $request->merge([Contact::PHONE => $phoneFormat]);

            //Check Duplicate Phone
            if (!empty($request->input(Contact::PHONE)) && $contact->phone != $request->input(Contact::PHONE)) {
                $contactByPhone = Contact::where(Contact::PHONE, $request->input(Contact::PHONE))->get();
                if (count($contactByPhone) > 0) {
                    return $this->responseValidation(ErrorCode::PHONE_EXISTS);
                }
            }

            //Check Duplicate Email
            if (!empty($request->input(Contact::EMAIL)) && $contact->email != $request->input(Contact::EMAIL)) {
                $contactByEmail = Contact::where(Contact::EMAIL, $request->input(Contact::EMAIL))->get();
                if (count($contactByEmail) > 0) {
                    return $this->responseValidation(ErrorCode::EMAIL_EXISTS);
                }
            }

            //Check Duplicate Facebook
            if (!empty($request->input(Contact::SOCIAL_ID)) && $contact->social_id != $request->input(Contact::SOCIAL_ID)) {
                $contactBySocialId = Contact::where(Contact::SOCIAL_ID, $request->input(Contact::SOCIAL_ID))->get();
                if (count($contactBySocialId) > 0) {
                    return $this->responseValidation(ErrorCode::SOCIAL_EXISTS);
                }
            }

            //Check Duplicate Google
            if (!empty($request->input(Contact::GOOGLE)) && $contact->google != $request->input(Contact::GOOGLE)) {
                $contactByGoogle = Contact::where(Contact::GOOGLE, $request->input(Contact::GOOGLE))->get();
                if (count($contactByGoogle) > 0) {
                    return $this->responseValidation(ErrorCode::GOOGLE_EXISTS);
                }
            }

            //Check Duplicate Apple ID
            if (!empty($request->input(Contact::APPLE_ID)) && $contact->apple_id != $request->input(Contact::APPLE_ID)) {
                $contactByAppleId = Contact::where(Contact::APPLE_ID, $request->input(Contact::APPLE_ID))->get();
                if (count($contactByAppleId) > 0) {
                    return $this->responseValidation(ErrorCode::APPLE_ID_EXISTS);
                }
            }


            //Set Data
            $contact->{Contact::FULLNAME} = $request->input(Contact::FULLNAME);
            $contact->{Contact::GENDER} = $request->input(Contact::GENDER);
            $contact->{Contact::PHONE} = $request->input(Contact::PHONE);
            $contact->{Contact::EMAIL} = $request->input(Contact::EMAIL);
            $contact->{Contact::SOCIAL_ID} = $request->input(Contact::SOCIAL_ID);
            $contact->{Contact::GOOGLE} = $request->input(Contact::GOOGLE);
            $contact->{Contact::APPLE_ID} = $request->input(Contact::APPLE_ID);

            if ($contact->save()) {
                if (!empty($request['profile_image'])) {
                    $contact->profile_image = StringHelper::editImage($request['profile_image'], $request['old_profile_image'], ImagePath::contactImagePath);
                    $contact->save();
                }

                if (!empty($request['cover_image'])) {
                    $contact->cover_image = StringHelper::editImage($request['cover_image'], $request['old_cover_image'], ImagePath::contactImagePath);
                    $contact->save();
                }

                if (!empty($request['signature_image'])) {
                    $contact->signature_image = StringHelper::editImage($request['signature_image'], $request['old_signature_image'], ImagePath::contactSignatureImagePath);
                    $contact->save();
                }
            } else {
                return $this->responseValidation(ErrorCode::ACTION_FAILED);
            }

            return $this->responseWithData($contact);
        } else {
            return $this->responseValidation(ErrorCode::INVALID);
        }
    }

    /**
     * Update User Image
     */
    public function updateUserImage(Request $request)
    {
        //Check Validation
        $this->validate($request, [
            'id' => 'required|exists:contact,id',
            'profile_image' => 'required',
            'old_profile_image' => 'required',
            'cover_image' => 'required',
            'old_cover_image' => 'required'
        ]);

        $contact = Contact::find($request->input('id'));

        if (!empty($contact)) {
            if (!empty($request['profile_image'])) {
                $contact->profile_image = StringHelper::editImage($request['profile_image'], $request['old_profile_image'], ImagePath::contactImagePath);
                $contact->save();
            }

            if (!empty($request['cover_image'])) {
                $contact->cover_image = StringHelper::editImage($request['cover_image'], $request['old_cover_image'], ImagePath::contactImagePath);
                $contact->save();
            }
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }

        return $this->responseWithData();
    }

    /**
     * Update User Country
     */
    public function updateUserCountry(Request $request)
    {
        //Check Validation
        $this->validate($request, [
            'id' => 'required|exists:contact,id',
            'country_id' => 'required|exists:country,id'
        ]);

        $contact = Contact::find($request->input('id'));

        if (!empty($contact)) {
            $contact->country_id = $request->input('country_id');
            $contact->save();
        } else {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }

        return $this->responseWithData();
    }

    /**
     * Get Contact
     */
    public function getSupport()
    {
        $data = Support::orderBy('id', 'desc')
            ->select(
                'support_type',
                'support_value'
            )
            ->get();

        $sort = [];
        foreach ($data as $item) {
            $sort[$item->support_type] = $item->support_value;
        }
        return $this->responseWithData($sort);
    }

    /**
     * Get Privacy Policy
     *
     */
    public function getPrivacyPolicy()
    {
        $title = 'privacy policy';
        $data = Post::getPageContent($title)->first();

        return $this->responseWithData($data);
    }


}
