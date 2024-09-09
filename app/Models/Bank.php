<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{

    const TABLE_NAME = 'bank';
    const ID = 'id';
    const NAME = 'name';
    const IMAGE = 'image';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    //BankAccount Relationship
    public function bankAccount()
    {
        return $this->hasMany(BankAccount::class, BankAccount::BANK_ID, self::ID);
    }

    //Lists
    public static function lists($filter = [])
    {
        return self::select(
            'bank.id',
            'bank.name',
            'bank.image',
        );
    }

    //Set Data
    public function setData($data)
    {
        $this->{self::NAME} = $data[self::NAME];
    }

}
