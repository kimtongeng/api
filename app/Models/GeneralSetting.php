<?php

namespace App\Models;

use App\Enums\Types\GeneralSettingKey;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    const TABLE_NAME = 'setting';
    const ID = 'id';
    const KEY = 'key';
    const VALUE = 'value';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;


    public static function lists()
    {
        return self::all();
    }

    //Set Data
    public function setData($data)
    {
        $this->{self::KEY} = $data[self::KEY];
        $this->{self::VALUE} = $data[self::VALUE];
    }

    //Get Property Transaction Fee
    public static function getPropertyTransactionFee()
    {
        $fee = 0;
        $data = self::select(self::VALUE)->where(self::KEY, GeneralSettingKey::getPropertyTransactionFee())->first();
        if (!empty($data)) {
            $fee = $data->{self::VALUE};
        }
        return $fee;
    }

    //Get Property Transaction Fee
    public static function getTransactionPaymentDeadline()
    {
        $deadline = 0;
        $data = self::select(self::VALUE)->where(self::KEY, GeneralSettingKey::getTransactionPaymentDeadline())->first();
        if (!empty($data)) {
            $deadline = $data->{self::VALUE};
        }
        return $deadline;
    }

    //Get Security Code
    public static function getSecurityCode()
    {
        $code = 0;
        $data = self::select(self::VALUE)->where(self::KEY, GeneralSettingKey::getSecurityCode())->first();
        if (!empty($data)) {
            $code = $data->{self::VALUE};
        }
        return $code;
    }

    //Get API Version
    public static function getAPIVersion()
    {
        $version = 0;
        $data = self::select(self::VALUE)->where(self::KEY, GeneralSettingKey::getAPIVersion())->first();
        if (!empty($data)) {
            $version = $data->{self::VALUE};
        }
        return $version;
    }
}
