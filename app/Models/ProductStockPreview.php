<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockPreview extends Model
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
        'selling_price',
        'purchase_price',
        'status',
        'error'
    ];

    protected $casts = [
        'status' => ProductStatus::class,
        'error' => 'array'
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
}
