<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class PaymentType extends Enum  {
    const CASH = 'cash';
    const TRANSFER = 'transfer';
    public static function asSelectArray(): array
    {
        return self::asArray();
    }
}
