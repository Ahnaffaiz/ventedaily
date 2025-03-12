<?php

namespace App\Models;

use App\Enums\ShippingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleShipping extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id', 'status', 'date', 'cost', 'no_resi', 'marketplace_id',
        'order_id_marketplace', 'customer_name', 'address', 'phone', 'city', 'bank_id', 'transfer_amount'
    ];

    protected $cast = [
        'status' => ShippingStatus::class
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }
}
