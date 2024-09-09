<?php

namespace App\Models;

use App\Enums\Types\NewsVisitorsStatus;
use Illuminate\Database\Eloquent\Model;

class NewsVisitors extends Model
{
    const TABLE_NAME = 'news_visitors';
    const ID = 'id';
    const NEWS_ID = 'news_id';
    const CONTACT_ID = 'contact_id';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::NEWS_ID} = $data[self::NEWS_ID];
        $this->{self::CONTACT_ID} = $data[self::CONTACT_ID];
        $this->{self::STATUS} = $data[self::STATUS];
    }

    public static function listNewsVisitors($filter = [])
    {
        $newsID = isset($filter['news_id']) ? $filter['news_id'] : null;
        $status = isset($filter['status']) ? $filter['status'] : null;
        $search = isset($filter['search']) ? $filter['search'] : null;

        return self::join('contact','contact.id', 'news_visitors.contact_id')
        ->join('business_category', 'business_category.id', 'contact.position_group_id')
        ->leftjoin('contact_business_info', 'contact.id', 'contact_business_info.contact_id')
        ->when($newsID, function ($query) use ($newsID) {
            $query->where('news_visitors.news_id', $newsID);
        })
        ->when($status, function ($query) use ($status) {
            $query->when($status == NewsVisitorsStatus::getJoin(), function ($query) {
                $query->where('news_visitors.status', NewsVisitorsStatus::getJoin());
            });
            $query->when($status == NewsVisitorsStatus::getLeave(), function ($query) {
                $query->where('news_visitors.status', NewsVisitorsStatus::getLeave());
            });
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('business_category.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('contact.fullname', 'LIKE', '%' . $search . '%');
            });
        })
        ->select(
            'news_visitors.id',
            'news_visitors.news_id',
            'news_visitors.contact_id',
            'contact.fullname as contact_name',
            'contact_business_info.image as contact_image',
            'contact.position_group_id',
            'business_category.name as position_group_name',
            'news_visitors.status',
            'news_visitors.created_at',
            'news_visitors.updated_at',
        )
        ->orderBy('news_visitors.id', 'desc');
    }
}
