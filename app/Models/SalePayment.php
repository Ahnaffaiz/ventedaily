<?php

namespace App\Models;

use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'sale_id', 'date', 'reference', 'amount', 'cash_received', 'cash_change', 'payment_type',
        'bank_id', 'account_number', 'account_name', 'desc'
    ];

    protected $casts = [
        'payment_type' => PaymentType::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
