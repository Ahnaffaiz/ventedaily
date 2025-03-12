<?php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class ShippingStatus extends Enum  {
    const SIAPKIRIM = 'siap kirim';
    const EKSPEDISI = 'ekspedisi';
    const SELESAI = 'setelai';
    public static function asSelectArray(): array
    {
        return array_combine(array_values(self::asArray()), array_values(self::asArray()));
    }
}
