<?php

namespace App\Http\Controllers\Admin\Common;

use App\Enums\Types\IsContactLogin;
use App\Models\User;
use App\Models\Contact;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Enums\Types\NotificationSendToPlatform;
use App\Http\Controllers\Controller;
use App\Models\ContactDevice;

class ChatController extends Controller
{
    const MODULE_KEY = 'chat';

    public function getAdminList()
    {
        $user = User::select(
            User::ID,
            User::FULL_NAME,
            User::IMAGE
        )
            ->orderBy(User::ID, 'desc')
            ->get();

        return $this->responseWithData($user);
    }

    public function getCustomerList(Request $request)
    {
        $search = isset($request['search']) ? $request['search'] : null;

        $customer = Contact::when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where(Contact::FULLNAME, 'LIKE', '%' . $search . '%')
                    ->orWhere(Contact::PHONE, 'LIKE', '%' . $search . '%')
                    ->orWhere(Contact::EMAIL, 'LIKE', '%' . $search . '%');
            });
        })
            ->select(
                Contact::ID,
                Contact::FULLNAME,
                Contact::PHONE,
                Contact::PROFILE_IMAGE
            )
            ->orderBy(User::ID, 'desc')
            ->get();

        return $this->responseWithData($customer);
    }

    public function sendNotificationToApp(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:contact,id',
            'text' => 'required'
        ]);

        $titleMessage = 'Chat from admin';
        $descriptionMessage = $request->input('text');
        $platForm = NotificationSendToPlatform::getMobile();

        $contact = Contact::find($request->input('id'));

        if (!empty($contact)) {
            $contactDevices = ContactDevice::where(ContactDevice::CONTACT_ID, $contact->id)
                ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                ->whereNotNull(ContactDevice::FCM_TOKEN)
                ->get();


            foreach ($contactDevices as $item) {
                $send = Notification::sendNotificationWhenChat(
                    $item[ContactDevice::FCM_TOKEN],
                    $titleMessage,
                    $descriptionMessage,
                    $platForm
                );
                info('From Admin => Chat: ' . $send);
            }

        }

        return $this->responseWithSuccess();
    }
}
