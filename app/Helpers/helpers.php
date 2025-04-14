<?php

use App\Models\ProductStockHistory;
use Illuminate\Support\Facades\Auth;

if (! function_exists('setStockHistory')) {
    /**
     * Mencatat riwayat perubahan stok produk.
     *
     * @param  int  $productStockId
     * @param  string  $stockType
     * @param  string  $stockActivity
     * @param  string  $status
     * @param  int  $stockBefore
     * @param  int  $stockAfter
     * @return \App\Models\ProductStockHistory|null
     */
    function setStockHistory(
        int $productStockId,
        string $stockActivity,
        string $status,
        string $fromStockType = NULL,
        string $toStockType = NULL,
        int $qty,
        string $reference = NULL,
        int $finalAllStock = 0,
        int $finalHomeStock = 0,
        int $finalStoreStock = 0,
        int $finalPreOrderStock = 0,
        bool $isTemporary = false
    ): ?ProductStockHistory {
        try {
            return ProductStockHistory::create([
                'product_stock_id' => $productStockId,
                'stock_activity' => $stockActivity,
                'status' => $status,
                'from_stock_type' => $fromStockType,
                'to_stock_type' => $toStockType,
                'qty' => $qty,
                'reference'=> $reference,
                'final_all_stock' => $finalAllStock,
                'final_home_stock' => $finalHomeStock,
                'final_store_stock' => $finalStoreStock,
                'final_pre_order_stock' => $finalPreOrderStock,
                'is_temporary' => $isTemporary,
                'user_id' => Auth::user()->id,
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }
}
