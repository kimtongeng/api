<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modifier extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'modifier';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const NAME = 'name';
    const CHOICE = 'choice';
    const IS_REQUIRED = 'is_required';
    const DESCRIPTION = 'description';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::NAME} = $data[self::NAME];
        $this->{self::CHOICE} = $data[self::CHOICE];
        $this->{self::IS_REQUIRED} = $data[self::IS_REQUIRED];
        isset($data[self::DESCRIPTION]) && $this->{self::DESCRIPTION} = $data[self::DESCRIPTION];
    }

    /*
     * Relationship Area
     * */
    //Modifier Option Relationship
    public function modifierOption()
    {
        return $this->hasMany(ModifierOption::class, ModifierOption::MODIFIER_ID, self::ID);
    }


    public static function lists($filter = [])
    {
        $search = isset($filter['search']) ? $filter['search'] : null;
        $modifierId = isset($filter['modifier_id']) ? $filter['modifier_id'] : null;
        $businessId = isset($filter['business_id']) ? $filter['business_id'] : null;

        return self::when($modifierId, function ($query) use ($modifierId) {
            $query->where('modifier.id', $modifierId);
        })
            ->when($businessId, function ($query) use ($businessId) {
                $query->where('modifier.business_id', $businessId);
            })
            ->when($search, function ($query) use ($search) {
                $query->where('modifier.name', 'LIKE', '%' . $search . '%');
            })
            ->select(
                'modifier.id',
                'modifier.business_id',
                'modifier.name',
                'modifier.choice',
                'modifier.is_required',
                'modifier.description'
            )
            ->with([
                'modifierOption' => function ($query) {
                    $query->select(
                        'modifier_option.id',
                        'modifier_option.modifier_id',
                        'modifier_option.name',
                        'modifier_option.price',
                    )
                        ->orderBy('modifier_option.id', 'DESC')
                        ->get();
                }
            ])
            ->orderBy('modifier.id', 'DESC');
    }

}
