<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookList extends Model
{
    const TABLE_NAME = 'book_list';
    const ID = 'id';
    const TRANSACTION_ID = 'transaction_id';
    const ROOM_ID = 'room_id';
    const PRICE = 'price';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public function setData($data) {
        $this->{self::TRANSACTION_ID} = $data[self::TRANSACTION_ID];
        $this->{self::ROOM_ID} = $data[self::ROOM_ID];
        $this->{self::PRICE} = $data[self::PRICE];
    }

    //Sum Total Price
    public static function sumPrice($transaction_id)
    {
        $sum_price = BookList::where('book_list.transaction_id', $transaction_id)
        ->selectRaw('SUM(price) as total_price')
        ->first();

        return $sum_price->total_price;
    }
}
