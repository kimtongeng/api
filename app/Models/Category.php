<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    const TABLE_NAME = 'category';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const NAME = 'name';
    const IMAGE = 'image';
    const PARENT_ID = 'parent_id';
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
        isset($data[self::PARENT_ID]) && $this->{self::PARENT_ID} = $data[self::PARENT_ID];
    }

    /**
     * Relationship Area
     */
    //Sub Category
    public function subCategory()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }
    //Place Price list
    public function PlacePriceList()
    {
        return $this->hasMany(PlacePriceList::class, PlacePriceList::CATEGORY_ID, self::ID);
    }

    /**
     * List Data Area
     */
    //List Category
    public static function listCategory($filter = [], $sortBy = '', $sortType = 'desc')
    {
        // Filter
        $business_ID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $parent_ID = isset($filter['parent_id']) ? $filter['parent_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::select(
            'category.id',
            'category.business_id',
            'category.name',
            'category.image',
            'category.parent_id',
            'category.created_at',
        )
        ->join('business','business.id','category.business_id')
        ->with('subCategory')
        ->when($business_ID,function ($query) use ($business_ID) {
            $query->where('category.business_id',$business_ID);
        })
        ->when($parent_ID, function ($query) use ($parent_ID) {
            $query->where('category.parent_id', $parent_ID);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('category.name', 'LIKE', '%' . $search . '%');
            });
        });
    }

    // List Attraction
    public static function listAttractionCategory($filter = [])
    {
        // Filter
        $business_ID = isset($filter['business_id']) ? $filter['business_id'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::select(
            'category.id',
            'category.business_id',
            'category.name',
            'category.created_at'
        )
        ->join('business', 'business.id', '=', 'category.business_id')
        ->when($business_ID, function ($query) use ($business_ID) {
            $query->where('category.business_id', $business_ID);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('category.name', 'LIKE', '%' . $search . '%');
            });
        })
        ->groupBy('category.id');
    }
}
