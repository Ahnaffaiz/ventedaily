<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = ['product_stock_id', 'total_items', 'price', 'total_price'];

    public function product_stock()
    {
        return $this->belongsTo(ProductStock::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}