<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'keep_id', 'user_id', 'customer_id', 'term_of_payment_id', 'discount_type', 'discount', 'discount_id', 'tax',
        'total_price', 'net_price', 'total_items', 'desc'
    ];

    protected $casts = [
        'discount_type' => 'enum:%,rupiah',
    ];

    public function keep()
    {
        return $this->belongsTo(Keep::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function term_of_payment()
    {
        return $this->belongsTo(TermOfPayment::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function sale_payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function sale_items()
    {
        return $this->hasMany(SaleItem::class);
    }
}