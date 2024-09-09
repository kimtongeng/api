<?php

namespace App\Enums\Types;

class AdminNotificationType
{
    //Declare Name And Value
    const OWNER_PAY_TRANSACTION_FEE = [
        'id' => 1,
        'name' => 'OWNER_PAY_TRANSACTION_FEE'
    ];

    //Get Value By Function Name (For Api)
    public static function getOwnerPayTransactionFee()
    {
        return self::OWNER_PAY_TRANSACTION_FEE['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::OWNER_PAY_TRANSACTION_FEE['name'] => self::OWNER_PAY_TRANSACTION_FEE['id'],
        ];
    }
}
