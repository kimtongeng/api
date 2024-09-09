<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppType extends Model
{

    const TABLE_NAME = 'app_type';
    const ID = 'id';
    const NAME = 'name';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //Get Combo List
    public static function getComboList()
    {
        return self::select(
            'id',
            'name'
        )
            ->get();
    }

    //Set Data
    public function setData($data)
    {
        $this->{self::NAME} = $data[self::NAME];
    }

}
