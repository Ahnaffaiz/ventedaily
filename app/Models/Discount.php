<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'value', 'discount_type', 'is_active'];

    protected $cast = [
        'discount_type' => DiscountType::class
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
