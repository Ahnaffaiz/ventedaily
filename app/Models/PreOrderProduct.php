<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'pre_order_id',
        'product_stock_id',
        'total_items',
        'selling_price',
        'purchase_price',
        'total_price'
    ];

    public function preOrder()
    {
        return $this->belongsTo(PreOrder::class);
    }

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }
}
