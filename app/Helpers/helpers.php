<?php

use App\Models\ProductStockHistory;

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
        string $stockType,
        string $stockActivity,
        string $status,
        int $stockBefore,
        int $stockAfter,
        string $stockTransferFrom = null,
        string $stockTransferTo = null,
    ): ?ProductStockHistory {
        try {
            return ProductStockHistory::create([
                'product_stock_id' => $productStockId,
                'stock_type' => $stockType,
                'stock_activity' => $stockActivity,
                'status' => $status,
                'stock_transfer_from' => $stockTransferFrom,
                'stock_transfer_to' => $stockTransferTo,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }
}
