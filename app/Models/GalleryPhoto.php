<?php

namespace App\Models;

use App\Enums\Types\IsResizeImage;
use App\Helpers\StringHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class GalleryPhoto extends Model
{
    const TABLE_NAME = 'gallery_photo';
    const ID = 'id';
    const IMAGE = 'image';
    const TYPE = 'type';
    const TYPE_ID = 'type_id';
    const ORDER = 'order';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //set data
    public function setData($data)
    {
        $this->{self::TYPE} = $data[self::TYPE];
        $this->{self::TYPE_ID} = $data[self::TYPE_ID];
        $this->{self::ORDER} = $data[self::ORDER];
        $this->{self::CREATED_AT} = Carbon::now();
        $this->{self::UPDATED_AT} = Carbon::now();
    }
}
