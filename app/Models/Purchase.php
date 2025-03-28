<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'supplier_id',
        'term_of_payment_id',
        'discount_type',
        'outstanding_balance',
        'discount',
        'tax',
        'ship',
        'total_price',
        'sub_total',
        'total_items',
        'desc',
    ];

    protected $cast = [
        'discount_type' => DiscountType::class
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function termOfPayment()
    {
        return $this->belongsTo(TermOfPayment::class);
    }

    public function purchasePayment()
    {
        return $this->hasOne(PurchasePayment::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
