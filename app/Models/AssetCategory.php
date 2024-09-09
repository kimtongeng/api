<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetCategory extends Model
{

    use SoftDeletes;

    const TABLE_NAME = 'asset_category';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const NAME = 'name';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    //Set Data
    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::NAME} = $data[self::NAME];
    }

    //list
    public static function lists($filter = [])
    {
        $search = isset($filter['search']) ? $filter['search'] : null;
        $propertyId = isset($filter['property_id']) ? $filter['property_id'] : null;

        return self::select(
            'id',
            'business_id as property_id',
            'name'
        )
            ->when($propertyId, function ($query) use ($propertyId) {
                $query->where('business_id', $propertyId);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'DESC');
    }
}
