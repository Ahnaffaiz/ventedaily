<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockInProduct extends Model
{
    use HasFactory;

    protected $fillable = ['stock_in_id', 'product_stock_id', 'stock'];

    public function stockIn()
    {
        return $this->belongsTo(StockIn::class);
    }

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }
}
