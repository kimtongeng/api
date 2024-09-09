<?php

namespace App\Http\Controllers\Mobile\Common;

use Exception;
use App\Helpers\FCM;
use App\Models\GroupChat;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ContactDevice;
use App\Helpers\Utils\ErrorCode;
use App\Models\GroupChatContact;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\IsContactLogin;
use Illuminate\Support\Facades\Log;
use App\Enums\Types\ChatContactType;
use App\Http\Controllers\Controller;
use App\Enums\Types\NotificationSendType;
use App\Enums\Types\NotificationSendToPlatform;

class ChatNotificationAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:mobile');
    }

    //Create Device Group Chat Notification
    public function createDeviceGroup(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required',
            'customer_id' => 'required',
            'title' => 'required',
            'message' => 'required',
            'contact_list' => 'required',
            'contact_list.*.contact_id' => 'required',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->input('contact_list') as $obj) {
                $notificationKeyName = 'customer-' . $request->input('customer_id') . '-business-' . $request->input('business_id') . '-agency-' . $obj['contact_id'];
                Log::info('Notification Key Name: ' . $notificationKeyName);
            }

            $group_chat_data = [
                GroupChat::BUSINESS_ID => $request->input('business_id'),
                GroupChat::CONTACT_ID => $request->input('customer_id'),
                GroupChat::NOTIFICATION_KEY_NAME => $notificationKeyName
            ];
            $group_chat = new GroupChat();
            $group_chat->setData($group_chat_data);

            if (!$group_chat->save()) {
                throw new \Exception('Failed to save group chat');
            }

            $fcmTokens = [];

            foreach ($request->input('contact_list') as $obj) {
                $group_chat_contact_data = [
                    GroupChatContact::GROUP_CHAT_ID => $group_chat->{GroupChat::ID},
                    GroupChatContact::CONTACT_ID => $obj['contact_id'],
                    GroupChatContact::CONTACT_TYPE => ChatContactType::getAgency(),
                ];
                $group_chat_contact = new GroupChatContact();
                $group_chat_contact->setData($group_chat_contact_data);
                $group_chat_contact->save();

                // Find FCM TOKEN Contact
                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $obj['contact_id'])
                ->whereNotNull(ContactDevice::FCM_TOKEN)
                    ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                    ->get();

                foreach ($contactDeviceData as $device) {
                    $fcmTokens[] = $device->{ContactDevice::FCM_TOKEN};
                }
            }

            if (empty($fcmTokens)) {
                throw new \Exception('No valid FCM tokens found');
            }

            // Create device group in FCM with all tokens at once
            $createResponse = FCM::createDeviceGroup($notificationKeyName, $fcmTokens);
            $createResponseData = json_decode($createResponse, true);

            if (!isset($createResponseData['notification_key'])) {
                throw new \Exception('Failed to create device group in FCM');
            }

            $notificationKey = $createResponseData['notification_key'];
            $title = $request->input('title');
            $message = $request->input('message');

            // Send the notification
            $sendResponse = Notification::sendNotificationToGroupChat(
                $notificationKey,
                $title,
                $message
            );
            $sendResponseData = json_decode($sendResponse, true);
            info('Send Notification Response: ', $sendResponseData);

            DB::commit();
            return $this->responseWithData([
                'business_id' => $group_chat->{GroupChat::BUSINESS_ID},
                'customer_id' => $group_chat->{GroupChat::CONTACT_ID}
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in createDeviceGroup: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function addUserToDeviceGroup(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required',
            'customer_id' => 'required',
            'contact_list' => 'required',
            'contact_list.*.contact_id' => 'required',
            'contact_list.*.contact_type' => 'nullable',
        ]);

        $group_chat = GroupChat::where('group_chat.business_id', $request->input('business_id'))
        ->where('group_chat.contact_id', $request->input('customer_id'))
        ->first();

        if (empty($group_chat)) {
            return $this->responseValidation(ErrorCode::ACTION_FAILED);
        }

        $notificationKeyName = $group_chat->notification_key_name;

        $get_token = FCM::getToken($notificationKeyName);
        if (empty($get_token)) {
            return response()->json(['error' => 'Failed to retrieve FCM token'], 500);
        }

        $tokens = json_decode($get_token, true);

        if (!is_array($tokens) || !isset($tokens['notification_key'])) {
            return response()->json(['error' => 'Invalid FCM token response'], 500);
        }

        try {
            $fcmTokens = [];

            foreach ($request->input('contact_list') as $obj) {
                $group_chat_contact_data = [
                    GroupChatContact::GROUP_CHAT_ID => $group_chat->{GroupChat::ID},
                    GroupChatContact::CONTACT_ID => $obj['contact_id'],
                    GroupChatContact::CONTACT_TYPE => $obj['contact_type'],
                ];
                $group_chat_contact = new GroupChatContact();
                $group_chat_contact->setData($group_chat_contact_data);
                $group_chat_contact->save();

                // Find FCM TOKEN Contact
                $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $obj['contact_id'])
                ->whereNotNull(ContactDevice::FCM_TOKEN)
                    ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                    ->get();

                foreach ($contactDeviceData as $device) {
                    $fcmTokens[] = $device->{ContactDevice::FCM_TOKEN};
                }
            }

            if (empty($fcmTokens)) {
                throw new \Exception('No valid FCM tokens found');
            }

            $addResponse = FCM::addDeviceGroup($notificationKeyName, $tokens['notification_key'], $fcmTokens);
            info($addResponse);
        } catch (Exception $e) {
            Log::error('Error Add User to Group Chat: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sendChatNotificationTo(Request $request)
    {
        $this->validate($request, [
            'business_id' => 'required',
            'customer_id' => 'required',
            'sender' => 'required',
            'title' => 'required',
            'message' => 'required',
        ]);

        $customerId = $request->input('customer_id');
        $businessId = $request->input('business_id');
        $sender = $request->input('sender');
        $title = $request->input('title');
        $message = $request->input('message');

        if ($sender == $customerId) {
            $group_chat = GroupChat::where('group_chat.business_id', $businessId)
                ->where('group_chat.contact_id', $customerId)
                ->first();

            if ($group_chat) {
                $notificationKeyName = $group_chat->notification_key_name;
                $get_token = FCM::getToken($notificationKeyName);
                $tokens = json_decode($get_token, true);

                if ($tokens) {
                    $notificationKey = $tokens['notification_key'];
                    $sendResponse = Notification::sendNotificationToGroupChat(
                        $notificationKey,
                        $title,
                        $message
                    );

                    info('Notification Send to Group Chat : ' . $sendResponse);
                } else {
                    info('No tokens found for the group chat.');
                }
            } else {
                info('No group chat found for the provided business and contact.');
            }
        } else {
            // Find FCM TOKEN Contact
            $contactDeviceData = ContactDevice::where(ContactDevice::CONTACT_ID, $customerId)
                ->whereNotNull(ContactDevice::FCM_TOKEN)
                ->where(ContactDevice::IS_LOGIN, IsContactLogin::getYes())
                ->get();

            foreach ($contactDeviceData as $item) {
                $dataKey = [
                    'platform' => NotificationSendToPlatform::getMobile(),
                    'type' => NotificationSendType::getChat(),
                    'business_id' => $businessId,
                    'customer_id' => $customerId
                ];

                $send = FCM::send(
                    $item[ContactDevice::FCM_TOKEN],
                    $title,
                    $message,
                    $dataKey
                );
            }

            info('Notification Send to Customer : ' . $send);
        }

        return $this->responseWithData([
            'business_id' => $businessId,
            'customer_id' => $customerId
        ]);
    }

}
