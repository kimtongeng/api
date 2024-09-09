<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    const TABLE_NAME = 'country';
    const ID = 'id';
    const COUNTRY_NAME = 'country_name';
    const COUNTRY_PHONE_CODE = 'country_phone_code';
    const COUNTRY_ISO_CODE = 'country_iso_code';
    const NATIONALITY = 'nationality';
    const OPTIONAL_NAME = 'optional_name';
    const OPTIONAL_NATIONALITY = 'optional_nationality';
    const OPTION = 'option';
    const ORDER = 'order';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::COUNTRY_NAME} = $data[self::COUNTRY_NAME];
        $this->{self::COUNTRY_ISO_CODE} = $data[self::COUNTRY_ISO_CODE];
        isset($data[self::COUNTRY_PHONE_CODE]) && $this->{self::COUNTRY_PHONE_CODE} = $data[self::COUNTRY_PHONE_CODE];
        isset($data[self::NATIONALITY]) && $this->{self::NATIONALITY} = $data[self::NATIONALITY];
        isset($data[self::OPTIONAL_NAME]) && $this->{self::OPTIONAL_NAME} = $data[self::OPTIONAL_NAME];
        isset($data[self::OPTIONAL_NATIONALITY]) && $this->{self::OPTIONAL_NATIONALITY} = $data[self::OPTIONAL_NATIONALITY];
        isset($data[self::OPTION]) && $this->{self::OPTION} = $data[self::OPTION];
        isset($data[self::ORDER]) && $this->{self::ORDER} = $data[self::ORDER];
        $this->{self::STATUS} = $data[self::STATUS];
        $this->{self::CREATED_BY} = Auth::user()->id;
        $this->{self::UPDATED_BY} = Auth::user()->id;
    }

    const cambodiaCountryName = 'Cambodia';

    public static function getCurrentContactCountryID()
    {
        $countryID = 0;
        $currentCountryID = Auth::guard('mobile')->user()->{Contact::COUNTRY_ID};

        if (!empty($currentCountryID)) {
            $countryID = $currentCountryID;
        }

        return $countryID;
    }

    public static function getCountryCambodiaID()
    {
        $countryID = 0;
        $countryData = self::where('country_name', 'LIKE', '%' . self::cambodiaCountryName . '%')
            ->select('id')
            ->first();

        if (!empty($countryData)) {
            $countryID = $countryData->id;
        }

        return $countryID;
    }

    //get Country List
    public static function listsCountry($filter = [], $sortBy = '', $sortType = 'desc')
    {
        //Filter
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::select(
            'id',
            'country_phone_code',
            'country_iso_code',
            'country_name',
            'nationality',
            DB::raw("CONCAT('{\"latin_name\":\"', country_name, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(optional_name, '$.country_local_name')), '\"}') as optional_name"),
            DB::raw("CONCAT('{\"latin_name\":\"', nationality, '\",\"local_name\":\"', JSON_UNQUOTE(JSON_EXTRACT(optional_nationality, '$.country_local_nationality')), '\"}') as optional_nationality"),
            'option',
            'order',
            'status',
            'created_at',
        )
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('country.country_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('country.country_iso_code', 'LIKE', '%' . $search . '%');
            });
        })
        ->orderBy('country.order', 'ASC');
    }
}
