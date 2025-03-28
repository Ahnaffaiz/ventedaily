<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class KeepType extends Enum  {
    const REGULAR = 'regular';
    const CUSTOM = 'custom';
    public static function asSelectArray(): array
    {
        $array = self::asArray();
        $formatted = [];

        foreach ($array as $key => $value) {
            $formatted[$value] = ucwords(str_replace('_', ' ', $value));
        }

        return $formatted;
    }

    public static function getLabel(string $value): string
    {
        return ucwords(str_replace('_', ' ', $value));
    }
}
