<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lib extends Model
{
    const VAL_FAIL = 'Validation failed.';
    const PER_FAIL = 'Insufficient permission.';
    const SUCCESSFULLY = 'Your action has been completed successfully.';

    const YES = 'YES';
    const NO = 'NO';

    public static function getConfirm()
    {
        return [
            self::YES,
            self::NO
        ];
    }
}
