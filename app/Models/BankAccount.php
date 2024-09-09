<?php

namespace App\Models;

use App\Enums\Types\BankAccountStatus;
use App\Enums\Types\IsResizeImage;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ImagePath;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;

class BankAccount extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'bank_account';
    const ID = 'id';
    const CONTACT_ID = 'contact_id';
    const CONTACT_TYPE = 'contact_type';
    const BANK_ID = 'bank_id';
    const ACCOUNT_NAME = 'account_name';
    const ACCOUNT_NUMBER = 'account_number';
    const ACCOUNT_QR_CODE = 'account_qr_code';
    const LINK_ACCOUNT = 'link_account';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    //List
    public static function lists($filter = [] , $sortBy = '' , $sortType = 'desc')
    {
        // filter
        $contactID = isset($filter['contact_id']) ? $filter['contact_id'] : null;
        $contactType = isset($filter['contact_type']) ? $filter['contact_type'] : null;
        $bankID = isset($filter['bank_id']) ? $filter['bank_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;
        $status = $status == "0" ? 2 : $status;

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        //sort
        $sortAccountName   = $sortBy == 'account_name' ? 'account_name' : null;
        $sortAccountNumber = $sortBy == 'account_number' ? 'account_number' : null;
        $sortCreatedAt     = $sortBy == 'created_at' ? 'created_at' : null;
        $sortStatus        = $sortBy == 'status' ? 'status' : null;


        return self::join('bank', 'bank.id', 'bank_account.bank_id')
            ->join('contact', 'contact.id', 'bank_account.contact_id')
            ->when($contactID, function ($query) use ($contactID) {
                $query->where('contact.id', $contactID);
            })
            ->when($contactType, function ($query) use ($contactType) {
                $query->where('bank_account.contact_type', $contactType);
            })
            ->when($bankID, function ($query) use ($bankID) {
                $query->where('bank.id', $bankID);
            })
            ->when($status, function ($query) use ($status) {
                if ($status == 2) {
                    $query->where('bank_account.status', BankAccountStatus::getDisabled());
                } else {
                    $query->where('bank_account.status', BankAccountStatus::getEnabled());
                }
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('bank_account.account_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('bank_account.account_number', 'LIKE', '%' . $search . '%');
                });
            })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('bank_account.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($sortAccountName, function($query) use ($sortType) {
                $query->orderBy('bank_account.account_name' , $sortType);
            })
            ->when($sortAccountNumber, function ($query) use ($sortType) {
                $query->orderBy('bank_account.account_number', $sortType);
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy('bank_account.created_at', $sortType);
            })
            ->when($sortStatus, function ($query) use ($sortType) {
                $query->orderBy('bank_account.status', $sortType);
            })
            ->select(
                'bank_account.id',
                'contact.id as contact_id',
                'bank.id as bank_id',
                'bank.name as bank_name',
                'bank.image as bank_image',
                'bank_account.account_name',
                'bank_account.account_number',
                'bank_account.account_qr_code',
                'bank_account.link_account',
                'bank_account.status',
                'bank_account.created_at',
            );
    }

    //Set Data
    public function setData($data)
    {
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::CONTACT_TYPE} = $data[self::CONTACT_TYPE];
        $this->{self::BANK_ID} = $data[self::BANK_ID];
        $this->{self::ACCOUNT_NAME} = $data[self::ACCOUNT_NAME];
        $this->{self::ACCOUNT_NUMBER} = $data[self::ACCOUNT_NUMBER];
        isset($data[self::LINK_ACCOUNT]) && $this->{self::LINK_ACCOUNT} = $data[self::LINK_ACCOUNT];
        isset($data[self::STATUS]) && $this->{self::STATUS} = $data[self::STATUS];
    }
}
