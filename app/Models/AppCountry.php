<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class AppCountry extends Model
{
    const TABLE_NAME = 'app_country';
    const ID = 'id';
    const NAME = 'name';
    const KEY = 'key';
    const IMAGE = 'image';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::NAME} = $data[self::NAME];
        $this->{self::KEY} = $data[self::KEY];
    }

    public static function lists($filter = [])
    {
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::select(
            'id',
            'name',
            'key',
            'image',
            'created_at',
            DB::raw('NULL as value')
        )
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('app_country.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('app_country.key', 'LIKE', '%' . $search . '%');
            });
        });
    }
}
