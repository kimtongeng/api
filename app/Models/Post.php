<?php

namespace App\Models;

use App\Enums\Types\ContentCategoryType;
use App\Enums\Types\FavoriteType;
use App\Enums\Types\IsResizeImage;
use App\Helpers\StringHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use function auth;

class Post extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'post';
    const ID = 'id';
    const CATEGORY_ID = 'category_id';
    const POST_BY = 'post_by';
    const TITLE = 'title';
    const IMAGE_THUMBNAIl = 'image_thumbnail';
    const SHORT_DESC = 'short_desc';
    const FULL_DESC = 'full_desc';
    const ORDER = 'order';
    const VIEW_COUNT = 'view_count';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    //set data
    public function setData($data)
    {
        $this->{self::TITLE} = $data[self::TITLE];
        $this->{self::CATEGORY_ID} = $data[self::CATEGORY_ID];
        $this->{self::POST_BY} = auth()->user()->id;
        $this->{self::SHORT_DESC} = $data[self::SHORT_DESC];
        $this->{self::FULL_DESC} = $data[self::FULL_DESC];
        $this->{self::ORDER} = $data[self::ORDER];
        $this->{self::STATUS} = $data[self::STATUS];
    }

    /**
     * Get List (All Type)
     *
     */
    public static function lists($filter = [], $sort = null, $type = null)
    {
        //Filter
        $search = isset($filter['search']) ? $filter['search'] : null;
        $categoryId = isset($filter['category_id']) ? $filter['category_id'] : null;

        return Post::join('content_category', 'content_category.id', 'post.category_id')
            ->join('users', 'users.id', 'post.post_by')
            ->where('content_category.type', $type)
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('post.category_id', $categoryId);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('post.title', 'LIKE', '%' . $search . '%')
                        ->orWhere('post.created_at', 'LIKE', '%' . $search . '%')
                        ->orWhere('post.short_desc', 'LIKE', '%' . $search . '%')
                        ->orWhere('post.full_desc', 'LIKE', '%' . $search . '%')
                        ->orWhere('content_category.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('users.full_name', 'LIKE', '%' . $search . '%');
                });
            })
            ->select(
                'post.id',
                'post.title',
                'post.created_at',
                'content_category.id as category_id',
                'content_category.name as category_name',
                'post.post_by as post_by_id',
                'users.full_name as post_by_name',
                'post.image_thumbnail',
                'post.short_desc',
                'post.full_desc',
                'post.view_count'
            )
            ->groupBy('post.id');
    }

    /**
     * Get Page Content
     *
     */
    public static function getPageContent($title)
    {
        return self::lists([], null, ContentCategoryType::getPageContent())
            ->select(
                'post.id',
                'post.title',
                'post.full_desc as description',
            )
            ->where('post.title', 'LIKE', '%' . $title . '%');
    }
}
