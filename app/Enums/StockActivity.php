<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class StockActivity extends Enum {
    const PURCHASE = 'purchase';
    const KEEP = 'keep';
    const PRE_ORDER = 'pre order';
    const SALES = 'sales';
    const TRANSFER = 'transfer';
    const STOCK_IN = 'stock in';
    const RETUR = 'retur';
    const ADD = 'add';
    const REMOVE = 'remove';
    const IMPORT = 'import';

    const EDIT = 'edit';

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
