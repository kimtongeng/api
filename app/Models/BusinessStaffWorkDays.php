<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessStaffWorkDays extends Model
{
    use \Awobaz\Compoships\Compoships;

    const TABLE_NAME = 'business_staff_workdays';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const CONTACT_ID = 'contact_id';
    const DAY = 'day';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::DAY} = $data[self::DAY];
        $this->{self::CREATED_AT} = $data[self::CREATED_AT];
    }
}
