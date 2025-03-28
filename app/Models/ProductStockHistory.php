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
        'stock_type',
        'stock_activity',
        'stock_transfer_from',
        'stock_transfer_to',
        'status',
        'stock_before',
        'stock_after',
    ];

    protected $casts = [
        'stock_type' => StockType::class,
        'stock_activity' => StockActivity::class,
        'stock_transfer_from' => StockType::class,
        'stock_transfer_to' => StockType::class,
        'status' => StockStatus::class,
    ];

    /**
     * Get the productStock that owns the ProductStockHistory.
     */
    public function productStock(): BelongsTo
    {
        return $this->belongsTo(ProductStock::class);
    }
}
