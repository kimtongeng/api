<?php

namespace App\Models;

use App\Enums\Types\BankAccountContactType;
use App\Enums\Types\BankAccountStatus;
use Illuminate\Database\Eloquent\Model;

class BusinessAgencyBankAccount extends Model
{

    const TABLE_NAME = 'business_agency_bank_account';
    const ID = 'id';
    const BANK_ACCOUNT_ID = 'bank_account_id';
    const CONTACT_ID = 'contact_id';
    const BUSINESS_AGENCY_TYPE_ID = 'business_agency_type_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::BANK_ACCOUNT_ID} = $data[self::BANK_ACCOUNT_ID];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::BUSINESS_AGENCY_TYPE_ID} = $data[self::BUSINESS_AGENCY_TYPE_ID];
    }

    //List
    public static function lists($filter = [])
    {
        $contactID = isset($filter['contact_id']) ? $filter['contact_id'] : null;
        $businessAgencyTypeID = isset($filter['business_agency_type_id']) ? $filter['business_agency_type_id'] : null;

        return self::join('bank_account', 'bank_account.id', 'business_agency_bank_account.bank_account_id')
            ->join('bank', 'bank.id', 'bank_account.bank_id')
            ->when($contactID, function ($query) use ($contactID) {
                $query->where('business_agency_bank_account.contact_id', $contactID);
            })
            ->when($businessAgencyTypeID, function ($query) use ($businessAgencyTypeID) {
                $query->where('business_agency_bank_account.business_agency_type_id', $businessAgencyTypeID);
            })
            ->where('bank_account.contact_type', BankAccountContactType::getContact())
            ->where('bank_account.status', BankAccountStatus::getEnabled())
            ->select(
                'business_agency_bank_account.id',
                'business_agency_bank_account.contact_id',
                'bank.id as bank_id',
                'bank.name as bank_name',
                'bank.image as bank_image',
                'business_agency_bank_account.bank_account_id',
                'bank_account.account_name',
                'bank_account.account_number',
                'bank_account.account_qr_code',
                'bank_account.link_account',
            );
    }
}
