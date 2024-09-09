<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class RelatedDocument extends Model
{
    const TABLE_NAME = 'related_document';
    const ID = 'id';
    const DOC_TYPE_ID = 'doc_type_id';
    const BUSINESS_ID = 'business_id';
    const IMAGE = 'image';
    const ORDER = 'order';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //set data
    public function setData($data)
    {
        $this->{self::DOC_TYPE_ID} = $data[self::DOC_TYPE_ID];
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::ORDER} = $data[self::ORDER];
        $this->{self::CREATED_AT} = Carbon::now();
        $this->{self::UPDATED_AT} = Carbon::now();
    }
}
