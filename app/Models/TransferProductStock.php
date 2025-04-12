<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferProductStock extends Model
{
    use HasFactory;

    protected $fillable = ['transfer_stock_id', 'product_stock_id', 'stock', 'keep_product_id'];

    public function transferStock()
    {
        return $this->belongsTo(TransferStock::class);
    }

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }

    public function keepProduct()
    {
        return $this->belongsTo(KeepProduct::class);
    }
}
