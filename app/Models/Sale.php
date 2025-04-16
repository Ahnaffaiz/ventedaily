<?php

namespace App\Models;

use App\Enums\DiscountType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'keep_id', 'pre_order_id', 'user_id', 'customer_id', 'term_of_payment_id', 'discount_type', 'discount', 'discount_id', 'tax',
        'total_price', 'sub_total', 'total_items', 'desc', 'no_sale', 'ship', 'outstanding_balance', 'marketplace_id', 'order_id_marketplace'
    ];

    protected $cast = [
        'discount_type' => DiscountType::class
    ];

    public function keep()
    {
        return $this->belongsTo(Keep::class);
    }

    public function preOrder()
    {
        return $this->belongsTo(PreOrder::class);
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function termOfPayment()
    {
        return $this->belongsTo(TermOfPayment::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function salePayment()
    {
        return $this->hasOne(SalePayment::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function saleShipping()
    {
        return $this->hasOne(SaleShipping::class);
    }

    public function saleWithdrawal()
    {
        return $this->hasOne(SaleWithdrawal::class);
    }

    public function stockHistories()
    {
        return $this->morphMany(ProductStockHistory::class, 'reference');
    }

    public static function totalPriceToday()
    {
        $today = Carbon::now();
        return self::whereDate('created_at', $today)->sum('total_price');
    }

    public static function totalItemsToday()
    {
        $today = Carbon::now();
        return self::whereDate('created_at', $today)->sum('total_items');
    }

    public static function totalPriceResellerToday()
    {
        $today = Carbon::now();
        return self::whereHas('customer', function($query) {
            return $query->where('group_id', 1);
        })->whereDate('created_at', $today)->sum('total_price');
    }

    public static function totalItemsResellerToday()
    {
        $today = Carbon::now();
        return self::whereHas('customer', function($query) {
            return $query->where('group_id', 1);
        })->whereDate('created_at', $today)->sum('total_items');
    }

    public static function totalPriceOnlineToday()
    {
        $today = Carbon::now();
        return self::whereHas('customer', function($query) {
            return $query->where('group_id', 2);
        })->whereDate('created_at', $today)->sum('total_price');
    }

    public static function totalItemsOnlineToday()
    {
        $today = Carbon::now();
        return self::whereHas('customer', function($query) {
            return $query->where('group_id', 2);
        })->whereDate('created_at', $today)->sum('total_items');
    }
}
