<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleDistancePrice extends Model
{
    const TABLE_NAME = 'vehicle_distance_price';
    const ID = 'id';
    const VEHICLE_ID = 'vehicle_id';
    const MIN_DISTANCE = 'min_distance';
    const MAX_DISTANCE = 'max_distance';
    const PRICE = 'price';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data)
    {
        $this->{self::VEHICLE_ID} = $data[self::VEHICLE_ID];
        $this->{self::MIN_DISTANCE} = $data[self::MIN_DISTANCE];
        $this->{self::MAX_DISTANCE} = $data[self::MAX_DISTANCE];
        $this->{self::PRICE} = $data[self::PRICE];
    }
}
