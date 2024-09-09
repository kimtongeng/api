<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    const TABLE_NAME = 'district';
    const ID = 'id';
    const DISTRICT_NAME = 'district_name';
    const PROVINCE_ID = 'province_id';
    const ORDER = 'order';
    const OPTIONAL_NAME = 'optional_name';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';

    protected $table = self::TABLE_NAME;

    //set data
    public function setData($data)
    {
        $this->{self::DISTRICT_NAME} = $data[self::DISTRICT_NAME];
        $this->{self::PROVINCE_ID} = $data[self::PROVINCE_ID];
        $this->{self::OPTIONAL_NAME} = $data[self::OPTIONAL_NAME];
        isset($data[self::ORDER]) && $this->{self::ORDER} = $data[self::ORDER];
        $this->{self::STATUS} = $data[self::STATUS];
        $this->{self::CREATED_BY} = Auth::user()->id;
        $this->{self::UPDATED_BY} = Auth::user()->id;
    }

    //list
    public static function lists($filter = [])
    {
        $provinceID = isset($filter['province_id']) ? $filter['province_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::join('province', 'province.id', 'district.province_id')
            ->select(
                'district.id',
                DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as name"),
            )
            ->where('province.country_id', \App\Models\Country::getCountryCambodiaID())
            ->when($provinceID, function ($query) use ($provinceID) {
                $query->where('district.province_id', $provinceID);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('district.district_name', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy('district.district_name', 'ASC');
    }

    //list admin
    public static function listsAdmin($filter=[], $sortBy = "", $sortType="desc")
    {
        $provinceID = isset($filter['province_id']) ? $filter['province_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::join('province', 'province.id', 'district.province_id')
            ->select(
                'district.id',
                'province.id as province_id',
                'district.district_name',
                DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as optional_name"),
                DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}') as province_name"),
                'district.order',
                'district.status',
                'district.created_at'
            )
            ->when($provinceID, function ($query) use ($provinceID) {
                $query->where('district.province_id', $provinceID);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('district.district_name', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy('district.district_name', 'ASC');
    }

    public static function getDistrictNameById($district_id)
    {
        return self::select(
                DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as district_name"),
            )
            ->where('district.id', $district_id)
            ->first()
            ->district_name;
    }
}
