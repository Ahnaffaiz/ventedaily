<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class StockActivity extends Enum {
    const PURCHASE = 'purchase';
    const KEEP = 'keep';
    const PRE_ORDER = 'pre_order';
    const SALE = 'sale';
    const TRANSFER = 'transfer';
    const RETUR = 'retur';
    const ADD = 'add';
    const REMOVE = 'remove';

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
