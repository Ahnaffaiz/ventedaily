<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_pre_order',
        'customer_id',
        'user_id',
        'pre_order_type',
        'status',
        'pre_order_time',
        'total_items',
        'total_price',
        'desc'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function preOrderProducts()
    {
        return $this->hasMany(PreOrderProduct::class);
    }
}
