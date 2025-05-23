<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeepProduct extends Model
{
    use HasFactory;

    protected $fillable = ['keep_id', 'product_stock_id', 'total_items','home_stock', 'store_stock', 'selling_price', 'purchase_price', 'total_price'];

    public function keep()
    {
        return $this->belongsTo(Keep::class);
    }

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }

    public function transferProductStock()
    {
        return $this->hasMany(TransferProductStock::class, 'keep_product_id', 'id');
    }
}
