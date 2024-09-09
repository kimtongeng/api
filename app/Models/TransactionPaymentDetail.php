<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionPaymentDetail extends Model
{
    const TABLE_NAME = 'transaction_payment_detail';
    const ID = 'id';
    const TRANSACTION_PAYMENT_ID = 'transaction_payment_id';
    const TRANSACTION_ID = 'transaction_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;


    //set data
    public function setData($data)
    {
        $this->{self::TRANSACTION_PAYMENT_ID} = $data[self::TRANSACTION_PAYMENT_ID];
        $this->{self::TRANSACTION_ID} = $data[self::TRANSACTION_ID];
    }
}
