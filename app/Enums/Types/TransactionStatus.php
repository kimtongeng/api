<?php

namespace App\Enums\Types;

class TransactionStatus
{
    //Declare Name And Value
    const PENDING = [
        'id' => 1,
        'name' => 'PENDING'
    ];
    const APPROVED = [
        'id' => 2,
        'name' => 'APPROVED'
    ];
    const COMPLETED = [
        'id' => 3,
        'name' => 'COMPLETED'
    ];
    const REJECTED = [
        'id' => 4,
        'name' => 'REJECTED'
    ];
    const CANCELLED = [
        'id' => 5,
        'name' => 'CANCELLED'
    ];
    const PENDING_PAYMENT = [
        'id' => 6,
        'name' => 'PENDING_PAYMENT',
    ];
    const REJECT_PAYMENT = [
        'id' => 7,
        'name' => 'REJECT_PAYMENT',
    ];
    const AUDITING_PAYMENT = [
        'id' => 8,
        'name' => 'AUDITING_PAYMENT',
    ];

    //Get Value By Function Name (For Api)
    public static function getPending()
    {
        return self::PENDING['id'];
    }

    public static function getApproved()
    {
        return self::APPROVED['id'];
    }

    public static function getCompleted()
    {
        return self::COMPLETED['id'];
    }

    public static function getRejected()
    {
        return self::REJECTED['id'];
    }

    public static function getCancelled()
    {
        return self::CANCELLED['id'];
    }

    public static function getPendingPayment()
    {
        return self::PENDING_PAYMENT['id'];
    }

    public static function getRejectPayment()
    {
        return self::REJECT_PAYMENT['id'];
    }

    public static function getAuditingPayment()
    {
        return self::AUDITING_PAYMENT['id'];
    }

    //Get Value By Each Name (For Front)
    public static function getByEachName()
    {
        return [
            self::PENDING['name'] => self::PENDING['id'],
            self::APPROVED['name'] => self::APPROVED['id'],
            self::COMPLETED['name'] => self::COMPLETED['id'],
            self::REJECTED['name'] => self::REJECTED['id'],
            self::CANCELLED['name'] => self::CANCELLED['id'],
            self::PENDING_PAYMENT['name'] => self::PENDING_PAYMENT['id'],
            self::REJECT_PAYMENT['name'] => self::REJECT_PAYMENT['id'],
            self::AUDITING_PAYMENT['name'] => self::AUDITING_PAYMENT['id'],
        ];
    }
}
