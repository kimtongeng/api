<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ContactDevice extends Model
{
    const TABLE_NAME = 'contact_device';
    const ID = 'id';
    const CONTACT_ID = 'contact_id';
    const DEVICE_ID = 'device_id';
    const FCM_TOKEN = 'fcm_token';
    const IS_LOGIN = 'is_login';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;
    public $timestamps = false;


    public static function updateToken($device_id, $token)
    {
        $id = Auth::user()->id;
        self::where(self::CONTACT_ID, $id)
            ->where(self::DEVICE_ID, $device_id)
            ->update([
                self::FCM_TOKEN => $token
            ]);
    }

    //set data
    public function setData($data)
    {
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::DEVICE_ID} = $data[self::DEVICE_ID];
        $this->{self::FCM_TOKEN} = $data[self::FCM_TOKEN];
        $this->{self::IS_LOGIN} = $data[self::IS_LOGIN];
        isset($data[self::CREATED_AT]) && $this->{self::CREATED_AT} = $data[self::CREATED_AT];
        isset($data[self::UPDATED_AT]) && $this->{self::UPDATED_AT} = $data[self::UPDATED_AT];
    }
}
