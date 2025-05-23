<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class DiscountType extends Enum  {
    const PERSEN = 'persen';
    const RUPIAH = 'rupiah';
    public static function asSelectArray(): array
    {
        return self::asArray();
    }
}
