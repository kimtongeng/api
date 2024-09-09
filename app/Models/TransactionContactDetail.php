<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionContactDetail extends Model
{
    const TABLE_NAME = 'transaction_contact_detail';
    const ID = 'id';
    const TRANSACTION_ID = 'transaction_id';
    const BUSINESS_ID = 'business_id';
    const CONTACT_ID = 'contact_id';
    const START_TIME = 'start_time';
    const END_TIME = 'end_time';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //SetData
    public function setData($data)
    {
        $this->{self::TRANSACTION_ID} = $data[self::TRANSACTION_ID];
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        isset($data[self::START_TIME]) && $this->{self::START_TIME} = $data[self::START_TIME];
        isset($data[self::END_TIME]) && $this->{self::END_TIME} = $data[self::END_TIME];
    }
}
