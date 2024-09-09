<?php

namespace App\Models;

use App\Enums\Types\IsResizeImage;
use App\Helpers\StringHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PropertyCommissionWithdrawing extends Model
{
    const TABLE_NAME = 'property_commission_withdrawing';
    const ID = 'id';
    const PROPERTY_COMMISSION_ID = 'property_commission_id';
    const CONTACT_ID = 'contact_id';
    const WITHDRAW_DATE = 'withdraw_date';
    const WITHDRAW_AMOUNT = 'withdraw_amount';
    const TRANSACTION_DATE = 'transaction_date';
    const TRANSACTION_IMAGE = 'transaction_image';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //Lists
    public static function lists($filter = [])
    {
        //Filter
        $propertyCommissionID = isset($filter['property_commission_id']) ? $filter['property_commission_id'] : null;

        return self::leftjoin('contact', 'contact.id', 'property_commission_withdrawing.contact_id')
        ->when($propertyCommissionID, function ($query) use ($propertyCommissionID) {
            $query->where('property_commission_withdrawing.property_commission_id', $propertyCommissionID);
        })
            ->select(
                'property_commission_withdrawing.id',
                'property_commission_withdrawing.property_commission_id',
                'property_commission_withdrawing.withdraw_date',
                'property_commission_withdrawing.withdraw_amount',
                'property_commission_withdrawing.transaction_date',
                'property_commission_withdrawing.transaction_image',
                'contact.id as business_owner_id',
                'contact.fullname as business_owner_name',
                'property_commission_withdrawing.status',
            )
            ->orderBy('property_commission_withdrawing.withdraw_date', 'DESC');
    }

    //Set Data
    public function setData($data)
    {
        $this->{self::PROPERTY_COMMISSION_ID} = $data[self::PROPERTY_COMMISSION_ID];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::WITHDRAW_DATE} = Carbon::now();
        $this->{self::WITHDRAW_AMOUNT} = $data[self::WITHDRAW_AMOUNT];
        $this->{self::TRANSACTION_DATE} = $data[self::TRANSACTION_DATE];

    }
}
