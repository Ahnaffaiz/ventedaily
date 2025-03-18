<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id', 'transfer_amount', 'marketplace_price', 'withdrawal_amount', 'date'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function shipping()
    {
        return $this->hasOneThrough(SaleShipping::class, Sale::class);
    }
}
