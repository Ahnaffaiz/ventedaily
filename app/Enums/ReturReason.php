<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class ReturReason extends Enum  {
    const SWAP_ITEM = 'swap item';
    const SWAP_MONEY = 'swap money';
    const DEPOSIT = 'deposit';
    public static function asSelectArray(): array
    {
        return array_combine(array_values(self::asArray()), array_values(self::asArray()));
    }
}
