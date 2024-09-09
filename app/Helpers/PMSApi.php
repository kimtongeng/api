<?php

namespace App\Helpers;

class PMSApi
{

    public static function uploadBookingTransactionImage($image)
    {
        if (empty($image)) return null;

        $data = [
            "image" => $image
        ];

        $headers = [
            'Content-Type: application/json',
        ];

        $dataString = json_encode($data);
        $url = env('PMS_API_URL') . '/api/upload_booking_transaction_image';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);
        return $response;
    }

    public static function uploadAgencyImage($data)
    {
        if (empty($data)) return null;
        $headers = [
            'Content-Type: application/json',
        ];

        $dataString = json_encode($data);
        $url = env('PMS_API_URL') . '/api/upload_agency_image';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);
        return $response;
    }
}
