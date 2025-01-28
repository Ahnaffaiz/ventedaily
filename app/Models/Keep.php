<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keep extends Model
{
    use HasFactory;

    protected $fillable = ['no_keep', 'customer_id', 'user_id', 'keep_type', 'keep_time', 'total_item', 'total_price', 'desc'];

    protected $casts = [
        'keep_type' => 'enum:custom,regular',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function keep_products()
    {
        return $this->hasMany(KeepProduct::class);
    }

    public function sale()
    {
        return $this->hasOne(Sale::class);
    }
}