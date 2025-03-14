<?php

namespace App\Models;

use App\Enums\KeepStatus;
use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'imei',
        'code',
        'is_favorite',
        'status',
        'category_id',
        'desc',
        'image'
    ];

    protected $casts = [
        'status' => ProductStatus::class,
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function totalStock()
    {
        return $this->productStocks()->sum('all_stock');
    }

    public function homeStock()
    {
        return $this->productStocks()->sum('home_stock');
    }

    public function storeStock()
    {
        return $this->productStocks()->sum('store_stock');
    }

    public function preOrderStock()
    {
        return $this->productStocks()->sum('pre_order_stock');
    }

    public function homeStockInKeep()
    {
        return KeepProduct::whereHas('keep', function($query){
                $query->where('status', strtolower(KeepStatus::ACTIVE));
            })
            ->whereIn('product_stock_id', $this->productStocks()->pluck('id')->toArray())
            ->sum('home_stock');
    }

    public function storeStockInKeep()
    {
        return KeepProduct::whereHas('keep', function($query){
            $query->where('status', strtolower(KeepStatus::ACTIVE));
        })
        ->whereIn('product_stock_id', $this->productStocks()->pluck('id')->toArray())
        ->sum('store_stock');
    }

    public function allStockInKeep()
    {
        return KeepProduct::whereHas('keep', function($query){
            $query->where('status', strtolower(KeepStatus::ACTIVE));
        })
        ->whereIn('product_stock_id', $this->productStocks()->pluck('id')->toArray())
        ->sum('total_items');
    }
}
