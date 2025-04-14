<?php

namespace App\Models;

use App\Enums\StockActivity;
use App\Enums\StockStatus;
use App\Enums\StockType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_stock_id',
        'stock_activity',
        'status',
        'from_stock_type',
        'to_stock_type',
        'qty',
        'reference',
        'final_all_stock',
        'final_home_stock',
        'final_store_stock',
        'final_pre_order_stock',
        'is_temporary',
        'user_id'
    ];

    protected $casts = [
        'stock_type' => StockType::class,
        'stock_activity' => StockActivity::class,
        'status' => StockStatus::class,
        'from_stock_type' => StockType::class,
        'to_stock_type' => StockType::class,
    ];

    /**
     * Get the productStock that owns the ProductStockHistory.
     */
    public function productStock(): BelongsTo
    {
        return $this->belongsTo(ProductStock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
