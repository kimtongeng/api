<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Support extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'support';
    const ID = 'id';
    const SUPPORT_TYPE = 'support_type';
    const SUPPORT_VALUE = 'support_value';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';

    protected $table = self::TABLE_NAME;

    public function getId()
    {
        return $this->id;
    }

    public function createdByName()
    {
        return $this->hasOne('App\Models\User', 'created_by', 'id');
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
        $sortByType = $sortBy == 'support_type' ? 'support_type' : null;
        $sortCreatedBy = $sortBy == 'created_by' ? 'created_by' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;


        return self::when($search, function ($query) use ($search) {
            $query->where('support.support_type', 'LIKE', '%' . $search . '%')
                ->orWhere('support.support_value', 'LIKE', '%' . $search . '%')
                ->orWhere('support.created_at', 'LIKE', '%' . $search . '%')
                ->orWhere('users.full_name', 'LIKE', '%' . $search . '%');
        })
        ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
            $query->whereBetween('support.created_at', [$createdAtStartDate, $createdAtEndDate]);
        })
        ->when($sortByType, function($query) use ($sortType) {
            $query->orderBy('support.support_type', $sortType);
        })
        ->when($sortCreatedBy, function ($query) use ($sortType) {
            $query->orderBy('support.created_by', $sortType);
        })
        ->when($sortCreatedAt, function ($query) use ($sortType) {
            $query->orderBy('support.created_at', $sortType);
        })
        ->join('users', 'support.created_by', '=', 'users.id')
        ->orderBy('id', 'desc')
        ->select('support.*', 'users.full_name as created_by_name');
    }

    /**
     * Set Data
     *
     * @param [type] $data
     * @return void
     */
    public function setData($data)
    {
        $this->{self::SUPPORT_TYPE} = $data[self::SUPPORT_TYPE];
        $this->{self::SUPPORT_VALUE} = $data[self::SUPPORT_VALUE];
        $this->{self::CREATED_BY} = Auth::guard('admin')->user()->id;
        $this->{self::UPDATED_BY} = Auth::guard('admin')->user()->id;
    }
}
