<?php

namespace App\Helpers;

use App\Enums\Types\NotificationSendToPlatform;
use App\Enums\Types\NotificationSendType;
use App\Helpers\Utils\ImagePath;
use App\Models\Notification;

class FCM
{
    public static function send($sendTo = null, $title = null, $body = null, $data = [])
    {
        //Data Key
        $platForm = isset($data['platform']) ? $data['platform'] : NotificationSendToPlatform::getMobile();
        $type = isset($data['type']) ? $data['type'] : null;
        $notificationId = isset($data['notification_id']) ? $data['notification_id'] : null;
        $image = isset($data['image']) ? $data['image'] : null;
        $referenceId = isset($data['reference_id']) ? $data['reference_id'] : null;
        $businessTypeId = isset($data['business_type_id'])  ? $data['business_type_id'] : null;

        //Image And Icon Path
        $apiUrl = env('APP_URL');
        $imagePath = $apiUrl . DIRECTORY_SEPARATOR . ImagePath::notificationImagePath . DIRECTORY_SEPARATOR;
        $iconPath = $apiUrl . DIRECTORY_SEPARATOR . 'app_logo.jpg';

        //Data That Need When Send
        $data = [
            "to" => $sendTo,
            "priority" => "high", //Doing Process Background For Android
            "notification" => [
                "title" => $title,
                "body" => $body,
                "sound" => "default",
                "icon" => $iconPath,
                "image" => !empty($image) ? $imagePath . $image : null,
                "content_available" => true, //Doing Process Background For IOS
            ],
            "data" => [
                "notification_id" => $notificationId,
                "reference_id" => $referenceId,
                "type" => $type,
                'business_type' => $businessTypeId
            ]
        ];

        //Click Action On Admin
        $adminUrl = env('ADMIN_URL');
        $clickToPage = null;
        if (!empty($type) && $platForm == NotificationSendToPlatform::getWeb()) {
            if ($type == NotificationSendType::getChat()) {
                $clickToPage = $adminUrl . '/chat';
            } else {
                $clickToPage = $adminUrl . '/notifications/list';
            }

            $data['notification']['click_action'] = $clickToPage;

            info('Admin Click Action: ' . $data['notification']['click_action']);
        }

        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . env('SERVER_API_KEY'),
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);

        return $response;
    }

    public static function requestDeviceGroup($operation, $notificationKeyName, $notificationKey, $fcmTokens)
    {
        $data = [
            "operation" => $operation,
            "notification_key_name" => $notificationKeyName,
            "notification_key" => $notificationKey,
            "registration_ids" => $fcmTokens,
        ];

        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . env('SERVER_API_KEY'),
            'Content-Type: application/json',
            'project_id: ' . env('SENDER_ID')
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/notification');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);
        return $response;
    }

    public static function createDeviceGroup($notificationKeyName, $fcmTokens)
    {
        $notificationKey = null;
        return self::requestDeviceGroup("create", $notificationKeyName, $notificationKey, $fcmTokens);
    }

    public static function addDeviceGroup($notificationKeyName, $notificationKey, $fcmTokens)
    {
        return self::requestDeviceGroup("add", $notificationKeyName, $notificationKey, $fcmTokens);
    }

    public static function removeDeviceGroup($notificationKeyName, $notificationKey, $fcmTokens)
    {
        return self::requestDeviceGroup("remove", $notificationKeyName, $notificationKey, $fcmTokens);
    }

    public static function getToken($notificationKeyName)
    {
        $data = [];

        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . env('SERVER_API_KEY'),
            'Content-Type: application/json',
            'project_id: ' . env('SENDER_ID')
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/notification?notification_key_name=' . $notificationKeyName);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET'); // Use GET request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, null); // No data for GET request
        $response = curl_exec($ch);
        return $response;
    }

    public static function subscribeToTopic($topicName, $registrationToken)
    {
        $url = $registrationToken . "/rel/topics/" . $topicName;

        $headers = [
            'Authorization: key=' . env('SERVER_API_KEY'),
            'Content-Type: application/json',
            'project_id: ' . env('SENDER_ID')
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://iid.googleapis.com/iid/v1/' . $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        return $response;
    }

    public static function unSubscribeToTopic($topicName, $registrationToken)
    {
        $url = $registrationToken . "/rel/topics/" . $topicName;

        $headers = [
            'Authorization: key=' . env('SERVER_API_KEY'),
            'Content-Type: application/json',
            'project_id: ' . env('SENDER_ID')
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://iid.googleapis.com/iid/v1/' . $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        return $response;
    }
}
