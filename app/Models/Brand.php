<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'brand';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const NAME = 'name';
    const IMAGE = 'image';
    const DESCRIPTION = 'description';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    /**
     * Set Data
     */
    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::NAME} = $data[self::NAME];
        $this->{self::IMAGE} = $data[self::IMAGE];
        !empty($data[self::DESCRIPTION]) && $this->{self::DESCRIPTION} = $data[self::DESCRIPTION];
    }

    /**
     * Get List Product Brand
     */

    public static function listBrand($filter = [])
    {
        // filter
        $business_ID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::join('business','business.id','brand.business_id')
        ->when($business_ID, function ($query) use ($business_ID) {
            $query->where('brand.business_id', $business_ID);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('brand.name','LIKE','%'.$search.'%');
            });
        })
        ->select(
            'brand.id',
            'brand.business_id',
            'brand.name',
            'brand.image',
            'brand.description',
        );
    }

}
