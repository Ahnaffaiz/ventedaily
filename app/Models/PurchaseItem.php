<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'product_stock_id', 'total_items', 'price', 'total_price'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product_stock()
    {
        return $this->belongsTo(ProductStock::class);
    }
}