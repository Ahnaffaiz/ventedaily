<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'imei',
        'code',
        'is_favorite',
        'status',
        'category_id',
        'desc',
        'image'
    ];

    protected $casts = [
        'status' => ProductStatus::class,
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function totalStock()
    {
        return $this->productStocks()->sum('all_stock');
    }
}
