<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessBankAccount extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'business_bank_account';
    const ID = 'id';
    const BUSINESS_TYPE_ID = 'business_type_id';
    const BANK_ACCOUNT_ID = 'bank_account_id';
    const BUSINESS_ID = 'business_id';
    const CONTACT_ID = 'contact_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::BUSINESS_TYPE_ID} = $data[self::BUSINESS_TYPE_ID];
        $this->{self::BANK_ACCOUNT_ID} = $data[self::BANK_ACCOUNT_ID];
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
    }

}
