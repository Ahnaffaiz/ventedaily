<?php

namespace App\Models;

use App\Enums\StockType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    use HasFactory;

    protected $fillable = ['total_items', 'stock_type', 'user_id'];

    protected $casts = [
        'stock_type' => StockType::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockInProducts()
    {
        return $this->hasMany(StockInProduct::class);
    }
}
