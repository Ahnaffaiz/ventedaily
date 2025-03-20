<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferProductStock extends Model
{
    use HasFactory;

    protected $fillable = ['transfer_stock_id', 'product_stock_id', 'stock'];

    public function transferStock()
    {
        return $this->belongsTo(TransferStock::class);
    }

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }
}
