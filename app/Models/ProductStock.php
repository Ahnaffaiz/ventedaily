<?php

namespace App\Models;

use App\Enums\KeepStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size_id',
        'color_id',
        'all_stock',
        'home_stock',
        'store_stock',
        'pre_order_stock',
        'qc_stock',
        'vermak_stock',
        'selling_price',
        'purchase_price',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function keepProducts()
    {
        return $this->hasMany(KeepProduct::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function homeStockInKeep()
    {
        return KeepProduct::whereHas('keep', function($query){
                $query->where('status', strtolower(KeepStatus::ACTIVE));
            })
            ->where('product_stock_id', $this->id)
            ->sum('home_stock');
    }

    public function storeStockInKeep()
    {
        return KeepProduct::whereHas('keep', function($query){
            $query->where('status', strtolower(KeepStatus::ACTIVE));
        })
        ->where('product_stock_id', $this->id)
        ->sum('store_stock');
    }

    public function preOrderStockInUse()
    {
        return PreOrderProduct::whereHas('preOrder', function($query){
            $query->where('status', strtolower(KeepStatus::ACTIVE));
        })
        ->where('product_stock_id', $this->id)
        ->sum('total_items');
    }

    public function allStockInKeep()
    {
        return KeepProduct::whereHas('keep', function($query){
            $query->where('status', strtolower(KeepStatus::ACTIVE));
        })
        ->where('product_stock_id', $this->id)
        ->sum('total_items');
    }
}
