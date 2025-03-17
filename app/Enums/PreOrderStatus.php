<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class PreOrderStatus extends Enum  {
    const ACTIVE = 'active';
    const CANCELED = 'canceled';
    const SOLD = 'sold';
    public static function asSelectArray(): array
    {
        return self::asArray();
    }
}
