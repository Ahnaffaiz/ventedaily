<?php

namespace App\Models;

use App\Enums\KeepStatus;
use App\Enums\KeepType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keep extends Model
{
    use HasFactory;

    protected $fillable = ['no_keep', 'customer_id', 'user_id', 'keep_type', 'keep_time', 'marketplace_id', 'order_id_marketplace', 'total_items', 'total_price', 'desc', 'status'];

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

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public static function allTotalItems()
    {
        return self::where('status', KeepStatus::ACTIVE)->sum('total_items');
    }

    public static function onlineTotalItems()
    {
        return self::whereHas('customer', function($query) {
            return $query->where('group_id', 2);
        })->where('status', KeepStatus::ACTIVE)
        ->sum('total_items');
    }

    public static function resellerTotalItems()
    {
        return self::whereHas('customer', function($query) {
            return $query->where('group_id', 1);
        })->where('status', KeepStatus::ACTIVE)
        ->sum('total_items');
    }

    public static function allTotalPrice()
    {
        return self::where('status', KeepStatus::ACTIVE)->sum('total_price');
    }

    public static function onlineTotalPrice()
    {
        return self::whereHas('customer', function($query) {
            return $query->where('group_id', 2);
        })->where('status', KeepStatus::ACTIVE)
        ->sum('total_price');
    }

    public static function resellerTotalPrice()
    {
        return self::whereHas('customer', function($query) {
            return $query->where('group_id', 1);
        })->where('status', KeepStatus::ACTIVE)
        ->sum('total_price');
    }
}
