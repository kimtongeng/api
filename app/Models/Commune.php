<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    const TABLE_NAME = 'commune';
    const ID = 'id';
    const COMMUNE_NAME = 'commune_name';
    const DISTRICT_ID = 'district_id';
    const POSTAL_CODE = 'postal_code';
    const ORDER = 'order';
    const OPTIONAL_NAME = 'optional_name';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {   
        $this->{self::COMMUNE_NAME} = $data[self::COMMUNE_NAME];
        $this->{self::DISTRICT_ID} = $data[self::DISTRICT_ID];
        isset($data[self::POSTAL_CODE]) && $this->{self::POSTAL_CODE} = $data[self::POSTAL_CODE];
        $this->{self::OPTIONAL_NAME} = $data[self::OPTIONAL_NAME];
        isset($data[self::ORDER]) && $this->{self::ORDER} = $data[self::ORDER];
        $this->{self::STATUS} = $data[self::STATUS];
        $this->{self::CREATED_BY} = Auth::user()->id;
        $this->{self::UPDATED_BY} = Auth::user()->id;
    }

    //list
    public static function lists($filter = [])
    {
        $districtID = isset($filter['district_id']) ? $filter['district_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::join('district', 'district.id', 'commune.district_id')
            ->join('province', 'province.id', 'district.province_id')
            ->select(
                'commune.id',
                DB::raw("CONCAT('{\"latin_name\":\"', commune.commune_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(commune.optional_name, '$.commune_local_name')), '\"}') as name")
            )
            ->where('province.country_id', \App\Models\Country::getCountryCambodiaID())
            ->when($districtID, function ($query) use ($districtID) {
                $query->where('commune.district_id', $districtID);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('commune.commune_name', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy('commune.commune_name', 'ASC');
    }

    // list admin
    public static function listAdmin($filter = [], $sortBy = '', $sortType = 'desc')
    {
        $districtID = isset($filter['district_id']) ? $filter['district_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::join('district', 'district.id', 'commune.district_id')
            ->join('province', 'province.id', 'district.province_id')
            ->select(
                'commune.id',
                'commune.postal_code',
                'commune.district_id',
                'commune.commune_name',
                DB::raw("CONCAT('{\"latin_name\":\"', commune.commune_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(commune.optional_name, '$.commune_local_name')), '\"}') as optional_name"),
                DB::raw("CONCAT('{\"latin_name\":\"', district.district_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(district.optional_name, '$.district_local_name')), '\"}') as district_name"),
                'commune.order',
                'commune.status',
                'commune.created_at',
            )
            ->when($districtID, function ($query) use ($districtID) {
                $query->where('commune.district_id', $districtID);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('commune.commune_name', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy('commune.id', 'desc');
    }
}
