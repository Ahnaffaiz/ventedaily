<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturItem extends Model
{
    use HasFactory;
    protected $fillable = ['retur_id','product_stock_id', 'status', 'total_items', 'price', 'total_price'];

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }
}
