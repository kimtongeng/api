<?php


namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PrefixCode extends Model
{
    const TABLE_NAME = 'prefix_code';
    const ID = 'id';
    const TYPE = 'type';
    const PREFIX = 'prefix';
    const PREFIX_FORMAT = 'prefix_format';
    const CODE_LENGTH = 'code_length';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * TYPE OF PREFIX FOR SYSTEM
     */
    const PROPERTY = 'PROPERTY';
    const ASSET = 'ASSET';
    const TRANSACTION = 'TRANSACTION';
    const CONTACT = 'CONTACT';
    const AGENCY = 'AGENCY';
    const DELIVERY = 'DELIVERY';

    protected $table = self::TABLE_NAME;

    public static function getLists()
    {
        return self::all();
    }

    public function getId()
    {
        return $this->{self::ID};
    }

    public function getName()
    {
        return $this->{self::TYPE};
    }

    public function getPrefix()
    {
        return $this->{self::PREFIX};
    }

    public function getPrefixFormat()
    {
        return $this->{self::PREFIX_FORMAT};
    }

    public function getCodeLength()
    {
        return $this->{self::CODE_LENGTH};
    }

    //lists
    public static function lists($filter = [] , $sortBy = '' , $sortType = 'desc')
    {
        // filter
        $search = isset($filter['search']) ? $filter['search'] : null;

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        // sort
        $sortByType = $sortBy == 'type' ? 'type' : null;
        $sortPrefix = $sortBy == 'prefix' ? 'prefix' : null;
        $sortLength = $sortBy == 'code_length' ? 'code_length' : null;
        $sortCreatedAt = $sortBy = 'created_at' ? 'created_at' : null;

        return self::when($search, function ($query) use ($search) {
            $query->where(self::TYPE, 'LIKE', '%' . $search . '%')
                ->orWhere(self::PREFIX, 'LIKE', '%' . $search . '%');
        })
        ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
            $query->whereBetween('prefix_code.created_at', [$createdAtStartDate, $createdAtEndDate]);
        })
        ->when($sortByType, function($query) use ($sortType) {
            $query->orderBy('prefix_code.type', $sortType);
        })
        ->when($sortPrefix, function ($query) use ($sortType) {
            $query->orderBy('prefix_code.prefix', $sortType);
        })
        ->when($sortLength, function ($query) use ($sortType) {
            $query->orderBy('prefix_code.code_length', $sortType);
        })
        ->when($sortCreatedAt, function ($query) use ($sortType) {
            $query->orderBy('prefix_code.created_at', $sortType);
        })
        ->orderBy(self::ID, 'DESC');
    }

    //get combo list
    public static function getComboList()
    {
        return self::select(self::ID, self::TYPE)->get();
    }

    public function setData($data)
    {
        $this->{self::TYPE} = strtoupper($data[self::TYPE]);
        $this->{self::PREFIX} = strtoupper($data[self::PREFIX]);
        $this->{self::CODE_LENGTH} = strtoupper($data[self::CODE_LENGTH]);
    }

    //Get Auto Code
    public static function getAutoCode($table, $type)
    {
        $prefix = self::where(self::TYPE, $type)->first();

        $prefix_code = 'UN';
        $code_length = '5';
        if (!empty($prefix)) {
            $prefix_code = $prefix->prefix;
            $code_length = $prefix->code_length;
        }

        $data = DB::table($table)->select('code')->orderBy('code', 'DESC')->first();
        $code = 1;
        if (!empty($data)) {
            $data_code = substr($data->code, strlen($prefix_code), strlen($data->code));
            $code = intval($data_code) + 1;
        }
        return $prefix_code . str_pad($code, $code_length, 0, STR_PAD_LEFT);
    }

    //Get Auto Code By Business
    public static function getAutoCodeByBusiness($table, $type, $businessID)
    {
        $prefix = self::where(self::TYPE, $type)->first();

        $prefix_code = 'UN';
        $code_length = '5';
        if (!empty($prefix)) {
            $prefix_code = $prefix->prefix;
            $code_length = $prefix->code_length;
        }

        $data = DB::table($table)->where('business_id', $businessID)->select('code')->orderBy('code', 'DESC')->first();
        $code = 1;
        if (!empty($data)) {
            $data_code = substr($data->code, strlen($prefix_code), strlen($data->code));
            $code = intval($data_code) + 1;
        }

        $formattedCode = str_pad($code, $code_length, '0', STR_PAD_LEFT);

        return $prefix_code . str_pad($formattedCode, $code_length, '0', STR_PAD_LEFT);
    }

    //Get Auto Code By Customer
    public static function getAutoCodeDeliveryByCustomer($table, $type, $customerID)
    {
        $prefix = self::where(self::TYPE, $type)->first();

        $prefix_code = 'UN';
        $code_length = '5';
        if (!empty($prefix)) {
            $prefix_code = $prefix->prefix;
            $code_length = $prefix->code_length;
        }

        $data = DB::table($table)->where('contact_id', $customerID)->select('order_code')->orderBy('order_code', 'DESC')->first();
        $code = 1;
        if (!empty($data)) {
            $data_code = substr($data->order_code, strlen($prefix_code), strlen($data->order_code));
            $code = intval($data_code) + 1;
        }

        $formattedCode = str_pad($code, $code_length, '0', STR_PAD_LEFT);

        return $prefix_code . str_pad($formattedCode, $code_length, '0', STR_PAD_LEFT);
    }
}
