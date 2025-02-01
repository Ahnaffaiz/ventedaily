<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class ProductStatus extends Enum  {
    const ACTIVE = 'ACTIVE';
    const ARCHIVE = 'ARCHIVE';
    const DEFAULT = 'DEFAULT';
    public static function asSelectArray(): array
    {
        return self::asArray();
    }
}