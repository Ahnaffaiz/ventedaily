<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class ItemReturStatus extends Enum  {
    const VERMAK = 'vermak';
    const GRADE_B = 'grade b';
    public static function asSelectArray(): array
    {
        return array_combine(array_values(self::asArray()), array_values(self::asArray()));
    }
}
