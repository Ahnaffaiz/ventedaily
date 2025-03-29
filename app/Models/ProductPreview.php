<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPreview extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'imei',
        'code',
        'is_favorite',
        'status',
        'category_id',
        'desc',
        'image',
        'error',
    ];

    protected $casts = [
        'status' => ProductStatus::class,
        'error' => 'array'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
