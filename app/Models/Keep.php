<?php

namespace App\Models;

use App\Enums\KeepStatus;
use App\Enums\KeepType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keep extends Model
{
    use HasFactory;

    protected $fillable = ['no_keep', 'customer_id', 'user_id', 'keep_type', 'keep_time', 'total_items', 'total_price', 'desc', 'status'];

    protected $casts = [
        'keep_type' => KeepType::class,
        'status' => KeepStatus::class,
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function keepProducts()
    {
        return $this->hasMany(KeepProduct::class);
    }

    public function sale()
    {
        return $this->hasOne(Sale::class);
    }
}
