<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = ['product_stock_id', 'total_items', 'price', 'total_price', 'sale_id'];

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
