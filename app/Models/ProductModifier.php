<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductModifier extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'product_modifier';
    const ID = 'id';
    const PRODUCT_ID = 'product_id';
    const MODIFIER_ID = 'modifier_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::PRODUCT_ID} = $data[self::PRODUCT_ID];
        $this->{self::MODIFIER_ID} = $data[self::MODIFIER_ID];
    }

    /*
     * Relationship Area
     * */
    //Modifier Option Relationship
    public function modifierOption()
    {
        return $this->hasMany(ModifierOption::class, ModifierOption::MODIFIER_ID, self::MODIFIER_ID);
    }

}
