<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyType extends Model
{

    use SoftDeletes;

    const TABLE_NAME = 'property_type';
    const ID = 'id';
    const NAME = 'name';
    const TYPE = 'type';
    const IMAGE = 'image';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::NAME} = $data[self::NAME];
        $this->{self::TYPE} = $data[self::TYPE];
    }

    //list
    public static function lists($filter = [])
    {
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::select(
            'id',
            'name',
            'type',
            'image'
        )
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'ASC');
    }
}
