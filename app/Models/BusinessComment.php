<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessComment extends Model
{
    const TABLE_NAME = 'business_comment';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const CONTACT_ID = 'contact_id';
    const COMMENT = 'comment';
    const TYPE = 'type';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::TYPE} = $data[self::TYPE];
    }

    public static function listComment($filter = [])
    {
        $newsID = isset($filter['news_id']) ? $filter['news_id'] : null;

        return self::join('contact', 'contact.id', 'business_comment.contact_id')
            ->select(
                'business_comment.id',
                'business_comment.business_id as news_id',
                'business_comment.contact_id',
                'contact.fullname as contact_name',
                'contact.profile_image as contact_image',
                'business_comment.type',
                'business_comment.comment',
                'business_comment.created_at',
            )
            ->when($newsID, function ($query) use ($newsID) {
                $query->where('business_comment.business_id', $newsID);
            })
            ->orderBy('business_comment.created_at','desc');
    }
}
