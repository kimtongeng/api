<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserDevice extends Model
{
    const TABLE_NAME = 'user_device';
    const ID = 'id';
    const USER_ID = 'user_id';
    const DEVICE_ID = 'device_id';
    const FCM_TOKEN = 'fcm_token';

    protected $table = self::TABLE_NAME;
    public $timestamps = false;


    public static function updateToken($device_id, $token)
    {
        $id = Auth::guard('admin')->user()->id;
        self::where(self::USER_ID, $id)
            ->where(self::DEVICE_ID, $device_id)
            ->update([
                self::FCM_TOKEN => $token
            ]);
    }

    //set data
    public function setData($data)
    {
        $this->{self::USER_ID} = $data[self::USER_ID];
        $this->{self::DEVICE_ID} = $data[self::DEVICE_ID];
        $this->{self::FCM_TOKEN} = $data[self::FCM_TOKEN];
    }
}
