<?php

namespace App\Models;

use App\Helpers\StringHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;

class ContentCategory extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'content_category';
    const ID = 'id';
    const NAME = 'name';
    const IMAGE = 'image';
    const TYPE = 'type';
    const PARENT_ID = 'parent_id';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    public static function getComboList()
    {
        return self::select('id', 'name', 'type', 'parent_id')->orderBy('id', 'DESC')->get();
    }

    public static function lists($filter)
    {
        $txt_src = isset($filter['txt_src']) ? $filter['txt_src'] : null;

        return self::when($txt_src, function ($query) use ($txt_src) {
            $query->where('content_category.name', 'LIKE', '%' . $txt_src . '%');
        })
            ->select('content_category.*')
            ->orderBy('content_category.id', 'DESC');
    }

    //set data
    public function setData($data)
    {
        $this->{self::NAME} = $data[self::NAME];
        $this->{self::TYPE} = $data[self::TYPE];
        $this->{self::PARENT_ID} = $data[self::PARENT_ID];
        $this->{self::STATUS} = $data[self::STATUS];
    }
}
