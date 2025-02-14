<?php

namespace App\Models;

use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    //
    protected $fillable = [
        'purchase_id',
        'user_id',
        'date',
        'reference',
        'amount',
        'cash_received',
        'cash_change',
        'payment_type',
        'bank_id',
        'account_number',
        'account_name',
        'desc',
    ];

    protected $casts = [
        'payment_type' => PaymentType::class,
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
