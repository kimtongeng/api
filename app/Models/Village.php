<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    const TABLE_NAME = 'village';
    const ID = 'id';
    const VILLAGE_NAME = 'village_name';
    const COMMUNE_ID = 'commune_id';
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
        $this->{self::VILLAGE_NAME} = $data[self::VILLAGE_NAME];
        $this->{self::COMMUNE_ID} = $data[self::COMMUNE_ID];
        $this->{self::OPTIONAL_NAME} = $data[self::OPTIONAL_NAME];
        $this->{self::STATUS} = $data[self::STATUS];
        $this->{self::CREATED_BY} = Auth::user()->id;
        $this->{self::UPDATED_BY} = Auth::user()->id;
    }

    //Get Combo List
    public static function getComboList($orderBy = "DESC")
    {
        return self::join('commune', 'commune.id', 'village.commune_id')
            ->join('district', 'district.id', 'commune.district_id')
            ->join('province', 'province.id', 'district.province_id')
            ->select(
                'village.id',
                DB::raw("CONCAT('{\"latin_name\":\"', village.village_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(village.optional_name, '$.village_local_name')), '\"}') as name"),
                'village.commune_id'
            )
            ->where('province.country_id', \App\Models\Country::getCountryCambodiaID())
            ->orderBy('village.village_name', $orderBy)
            ->get();
    }

    //List Admin
    public static function listAdmin($filter = [], $sortBy = '', $sortType = 'desc')
    {
        $communeId = isset($filter['commune_id']) ? $filter['commune_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::join('commune', 'commune.id', 'village.commune_id')
            ->select(
                'village.id',
                'village.commune_id',
                'village.village_name',
                DB::raw("CONCAT('{\"latin_name\":\"', village.village_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(village.optional_name, '$.village_local_name')), '\"}') as optional_name"),
                DB::raw("CONCAT('{\"latin_name\":\"', commune.commune_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(commune.optional_name, '$.commune_local_name')), '\"}') as commune_name"),
                'village.status',
                'village.created_at',
            )
            ->when($communeId, function ($query) use ($communeId) {
                $query->where('village.commune_id', $communeId);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('village.village_name', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy('village.village_name', 'desc');
    }
}
