<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class KeepType extends Enum  {
    const REGULAR = 'regular';
    const CUSTOM = 'custom';
    public static function asSelectArray(): array
    {
        return self::asArray();
    }
}
