<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    const TABLE_NAME = 'province';
    const ID = 'id';
    const PROVINCE_NAME = 'province_name';
    const COUNTRY_ID = 'country_id';
    const OPTIONAL_NAME = 'optional_name';
    const ORDER = 'order';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::PROVINCE_NAME} = $data[self::PROVINCE_NAME];
        $this->{self::COUNTRY_ID} = $data[self::COUNTRY_ID];
        $this->{self::OPTIONAL_NAME} = $data[self::OPTIONAL_NAME];
        isset($data[self::ORDER]) && $this->{self::ORDER} = $data[self::ORDER];
        $this->{self::STATUS} = $data[self::STATUS];
        $this->{self::CREATED_BY} = Auth::user()->id;
        $this->{self::UPDATED_BY} = Auth::user()->id;
    }

    //list
    public static function lists($filter = [])
    {
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::select(
            'id',
            DB::raw("CONCAT('{\"latin_name\":\"', province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(optional_name, '$.province_local_name')), '\"}') as name"),
            'image'
        )  
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('province_name', 'LIKE', '%' . $search . '%');
            });
        })
        ->orderBy('province_name', 'ASC');
    }

    //list admin
    public static function listAdmin($filter = [], $sortBy = '', $sortType = 'desc')
    {
        $countryId = isset($filter['country_id']) ? $filter['country_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::join('country', 'country.id', 'province.country_id')
        ->select(
            'province.id',
            'country.id as country_id',
            'province.province_name',
            DB::raw("CONCAT('{\"latin_name\":\"', province.province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(province.optional_name, '$.province_local_name')), '\"}') as optional_name"),
            DB::raw("CONCAT('{\"latin_name\":\"', country.country_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(country.optional_name, '$.country_local_name')), '\"}') as country_name"),
            'province.image',
            'province.order',
            'province.status',
            'province.created_at'
        )
        ->when($countryId, function ($query) use ($countryId) {
            $query->where('province.country_id', $countryId);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('province.province_name', 'LIKE', '%' . $search . '%');
            });
        })
        ->orderBy('province.id', 'DESC');
    }

    public static function getProvinceNameById($province_id)
    {
        return self::select(
            DB::raw("CONCAT('{\"latin_name\":\"', province_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(optional_name, '$.province_local_name')), '\"}') as province_name"),
        )
        ->where('province.id', $province_id)
        ->first()
        ->province_name;
    }
}
