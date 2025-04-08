<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class StockStatus extends Enum {
    const ADD = 'add';
    const REMOVE = 'remove';
    const CHANGE = 'change';
    const CHANGE_REMOVE = 'change remove';
    const CHANGE_ADD = 'change add';

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
