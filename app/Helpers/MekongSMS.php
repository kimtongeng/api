<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class MekongSMS
{
    const STATUS = [
        'SUCCESS' => 0,
    ];

    public static function statusSuccess()
    {
        return self::STATUS['SUCCESS'];
    }

    public static function send($smsText, $phone)
    {
        $client = new Client();
        $res = $client->request('POST', env('MEKONG_SMS_URL'), [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                "username" => env('MEKONG_SMS_USERNAME'),
                "pass" => env('MEKONG_SMS_PASS'),
                "sender" => env('MEKONG_SMS_SENDER'),
                "smstext" => $smsText,
                "gsm" => $phone,
                "int" => env('MEKONG_SMS_INT'),
                "cd"  => ""
            ],
            'verify' => false
        ]);
        $res = (string)$res->getBody();
        $code = explode(' ', $res);
        $start = strpos($res, '[');
        $end = strpos($res, ']');
        $startIndex = min($start, $end);
        $length = abs($start - $end);
        $msg = substr($res, $startIndex + 1, $length - 1);
        return [
            'code' => $code[0],
            'msg' => $msg
        ];
    }

    public static function sendVerificationCode($verificationCode, $phone)
    {
        $text = $verificationCode . " is your verification code for Super-App.";
        return self::send($text, $phone);
    }

    public static function checkBalance()
    {
        $client = new Client();
        $res = $client->request('POST', env('MEKONG_SMS_BALANCE_URL'), [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                "username" => env('MEKONG_SMS_USERNAME'),
                "pass" => env('MEKONG_SMS_PASS')
            ],
            'verify' => false
        ]);
        info($res->getBody());
        return (string)$res->getBody();
    }

    public static function getReport($startDate, $endDate)
    {
        $client = new Client();
        $res = $client->request('GET', env('MEKONG_SMS_REPORT_URL') . '?username=' . env('MEKONG_SMS_USERNAME') . '&pass=' . env('MEKONG_SMS_PASS') . '&sd=' . $startDate . '&ed=' . $endDate . '', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'verify' => false
        ]);

        info($res->getBody());

        return (string)$res->getBody();
    }
}
