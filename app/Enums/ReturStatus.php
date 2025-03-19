<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class ReturStatus extends Enum  {
    const TAKEN = 'taken';
    const PROCESSING = 'processing';
    const BACK_TO_STOCK = 'back to stock';
    public static function asSelectArray(): array
    {
        return array_combine(array_values(self::asArray()), array_values(self::asArray()));
    }
}
