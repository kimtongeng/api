<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TransactionPayment extends Model
{
    const TABLE_NAME = 'transaction_payment';
    const ID = 'id';
    const CONTACT_ID = 'contact_id';
    const BUSINESS_TYPE_ID = 'business_type_id';
    const BANK_ACCOUNT_ID = 'bank_account_id';
    const ACCOUNT_NAME = 'account_name';
    const ACCOUNT_NUMBER = 'account_number';
    const TOTAL_PAYMENT = 'total_payment';
    const TRANSACTION_DATE = 'transaction_date';
    const IMAGE = 'image';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //Lists
    public static function lists($filter = [], $sort = '', $sortType = 'desc')
    {
        //Filter
        $businessTypeId = isset($filter['business_type_id']) ? $filter['business_type_id'] : null;
        $businessOwnerID = isset($filter['business_owner_id']) ? $filter['business_owner_id'] : null;
        $transactionDate = isset($filter['transaction_date']) ? $filter['transaction_date'] : null;

        $transactionDateRange = isset($filter['transaction_date_range']) ? $filter['transaction_date_range'] : null;
        $startDate = empty($transactionDateRange['startDate']) ? null : Carbon::parse($transactionDateRange['startDate'])->format('Y-m-d H:i:s');
        $endDate = empty($transactionDateRange['endDate']) ? null : Carbon::parse($transactionDateRange['endDate'])->format('Y-m-d H:i:s');

        //Sort
        $sortType = !empty($sortType) ? $sortType : null;
        $sortTransactionDate = $sort == 'transaction_date' ? 'transaction_date' : null;

        return self::join('contact', 'contact.id', 'transaction_payment.contact_id')
            ->join('business_type', 'business_type.id', 'transaction_payment.business_type_id')
            ->leftjoin('bank_account', 'bank_account.id', 'transaction_payment.bank_account_id')
            ->leftjoin('bank', 'bank.id', 'bank_account.bank_id')
            ->when($businessTypeId, function ($query) use ($businessTypeId) {
                $query->where('transaction_payment.business_type_id', $businessTypeId);
            })
            ->when($businessOwnerID, function ($query) use ($businessOwnerID) {
                $query->where('transaction_payment.contact_id', $businessOwnerID);
            })
            ->when($transactionDate, function ($query) use ($transactionDate) {
                $query->whereRaw("DATE(transaction_payment.transaction_date) = '" . $transactionDate . "'");
            })
            ->when(!empty($startDate) && !empty($endDate), function ($query) use ($startDate, $endDate) {
                $query->whereBetween('transaction_payment.transaction_date', [$startDate, $endDate]);
            })
            ->when(!empty($sortTransactionDate), function ($query) use ($sortType) {
                $query->orderBy("transaction_payment.transaction_date", $sortType);
            })
            ->when(empty($sortTransactionDate), function ($query) use ($sortType) {
                $query->orderBy('transaction_payment.id', $sortType);
            })
            ->select(
                'transaction_payment.id',
                'transaction_payment.contact_id',
                'contact.fullname as contact_name',
                'transaction_payment.business_type_id',
                'business_type.name as business_type_name',
                'bank.id as bank_id',
                'bank.name as bank_name',
                'bank.image as bank_image',
                'transaction_payment.account_name',
                'transaction_payment.account_number',
                'transaction_payment.total_payment',
                'transaction_payment.transaction_date',
                'transaction_payment.image',
            );
    }

    //set data
    public function setData($data)
    {
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::BUSINESS_TYPE_ID} = $data[self::BUSINESS_TYPE_ID];
        $this->{self::BANK_ACCOUNT_ID} = $data[self::BANK_ACCOUNT_ID];
        $this->{self::ACCOUNT_NAME} = $data[self::ACCOUNT_NAME];
        $this->{self::ACCOUNT_NUMBER} = $data[self::ACCOUNT_NUMBER];
        $this->{self::TOTAL_PAYMENT} = $data[self::TOTAL_PAYMENT];
        $this->{self::TRANSACTION_DATE} = $data[self::TRANSACTION_DATE];
    }
}
