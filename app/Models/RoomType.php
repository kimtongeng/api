<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomType extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'room_type';
    const ID = 'id';
    const BUSINESS_ID = 'business_id';
    const NAME = 'name';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE_NAME;

    /**
     * Relation Ship Area
     */
    //relationship Room
    public function roomList()
    {
        return $this->hasMany(Room::class , Room::ROOM_TYPE_ID , self::ID);
    }

    public function setData($data)
    {
        $this->{self::BUSINESS_ID} = $data[self::BUSINESS_ID];
        $this->{self::NAME} = $data[self::NAME];
    }

    //Get Room Type List
    public static function list()
    {
       return self::select(
        'room_type.id',
        'room_type.business_id',
        'room_type.name',
        'room_type.created_at',
       );
    }
}
