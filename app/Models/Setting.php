<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'address',
        'telp',
        'owner',
        'keep_timeout',
        'logo',
        'name',
        'keep_code',
        'keep_increment',
        'pre_order_code',
        'pre_order_increment',
        'retur_code',
        'retur_increment',
        'sale_code',
        'sale_increment'
    ];

    public function getStartTimeAttribute($value)
    {
        return date('H:i', strtotime($value));
    }
}
