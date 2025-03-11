<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class PaymentType extends Enum  {
    const CASH = 'cash';
    const TRANSFER = 'transfer';
    public static function asSelectArray(): array
    {
        return array_combine(array_values(self::asArray()), array_values(self::asArray()));
    }

}
