<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionContact extends Model
{
    const TABLE_NAME = 'transaction_contact';
    const ID = 'id';
    const TRANSACTION_ID = 'transaction_id';
    const BUSINESS_ID = 'business_id';
    const CONTACT_ID = 'contact_id';
    const PRICE = 'price';
    const TIP_AMOUNT = 'tip_amount';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::TRANSACTION_ID} = $data[self::TRANSACTION_ID];
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        isset($data[self::PRICE]) && $this->{self::PRICE} = $data[self::PRICE];
        isset($data[self::TIP_AMOUNT]) && $this->{self::TIP_AMOUNT} = $data[self::TIP_AMOUNT];
    }
}
