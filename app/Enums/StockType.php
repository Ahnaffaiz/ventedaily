<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class StockType extends Enum {
    const HOME_STOCK = 'home_stock';
    const STORE_STOCK = 'store_stock';
    const PRE_ORDER_STOCK = 'pre_order_stock';

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
