<?php

namespace App\Console\Commands;

use App\Enums\KeepStatus;
use App\Enums\StockActivity;
use App\Enums\StockStatus;
use App\Enums\StockType;
use App\Models\Keep;
use App\Models\ProductStock;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateKeepStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:keep-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Keep Status After Time Out';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keeps = Keep::where('keep_time', '<', Carbon::now())
            ->where('status', strtolower(KeepStatus::ACTIVE))->get();

        try {
            foreach ($keeps as $keep) {
                $isReseller = $keep->customer->group->name == 'Reseller';

                foreach ($keep?->keepProducts as $keepProduct) {
                    // Check if product has been transferred
                    $hasTransferred = \App\Models\TransferProductStock::whereJsonContains('keep_product_id', $keepProduct->id)->exists();

                    if ($hasTransferred) {
                        // Case A: Keep is active and product has been transferred
                        $transferProductStock = \App\Models\TransferProductStock::whereJsonContains('keep_product_id', $keepProduct->id)
                            ->with('transferStock')
                            ->first();

                        if ($transferProductStock) {
                            $stockTypeTransfer = $transferProductStock->transferStock->transfer_from;

                            // If stock becomes 0, delete the transfer record
                            if ($transferProductStock->stock - $keepProduct->$stockTypeTransfer == 0) {
                                $transferProductStock->delete();
                            } else {
                                // Get current keep_product_ids array
                                $keepProductIds = $transferProductStock->keep_product_id ?? [];

                                // Remove this specific keep_product_id from the array
                                if (is_array($keepProductIds)) {
                                    $keepProductIds = array_filter($keepProductIds, function ($id) use ($keepProduct) {
                                        return $id != $keepProduct->id;
                                    });
                                }

                                // Update with modified array instead of setting to null
                                $transferProductStock->update([
                                    'stock' => $transferProductStock->stock - $keepProduct->$stockTypeTransfer,
                                    'keep_product_id' => array_values($keepProductIds)
                                ]);
                            }
                        }

                        // Restore stock based on customer group
                        if ($isReseller) {
                            $keepProduct->productStock->update([
                                'all_stock' => $keepProduct->productStock->all_stock + $keepProduct->home_stock + $keepProduct->store_stock,
                                'store_stock' => $keepProduct->productStock->store_stock + $keepProduct->home_stock + $keepProduct->store_stock,
                            ]);

                            setStockHistory(
                                $keepProduct->productStock->id,
                                StockActivity::KEEP,
                                StockStatus::REMOVE,
                                StockType::STORE_STOCK,
                                NULL,
                                $keepProduct->home_stock + $keepProduct->store_stock,
                                $keep->no_keep,
                                $keepProduct->productStock->all_stock,
                                $keepProduct->productStock->home_stock,
                                $keepProduct->productStock->store_stock,
                                $keepProduct->productStock->pre_order_stock,
                            );
                        } else {
                            // For online customers
                            $keepProduct->productStock->update([
                                'all_stock' => $keepProduct->productStock->all_stock + $keepProduct->home_stock + $keepProduct->store_stock,
                                'home_stock' => $keepProduct->productStock->home_stock + $keepProduct->home_stock + $keepProduct->store_stock,
                            ]);

                            setStockHistory(
                                $keepProduct->productStock->id,
                                StockActivity::KEEP,
                                StockStatus::REMOVE,
                                StockType::HOME_STOCK,
                                NULL,
                                $keepProduct->home_stock + $keepProduct->store_stock,
                                $keep->no_keep,
                                $keepProduct->productStock->all_stock,
                                $keepProduct->productStock->home_stock,
                                $keepProduct->productStock->store_stock,
                                $keepProduct->productStock->pre_order_stock,
                            );
                        }
                    } else {
                        // Condition B: Keep is active but product hasn't been transferred
                        // Restore home_stock
                        if ($keepProduct->home_stock > 0) {
                            $keepProduct->productStock->update([
                                'all_stock' => $keepProduct->productStock->all_stock + $keepProduct->home_stock,
                                'home_stock' => $keepProduct->productStock->home_stock + $keepProduct->home_stock,
                            ]);

                            setStockHistory(
                                $keepProduct->productStock->id,
                                StockActivity::KEEP,
                                StockStatus::REMOVE,
                                StockType::HOME_STOCK,
                                NULL,
                                $keepProduct->home_stock,
                                $keep->no_keep,
                                $keepProduct->productStock->all_stock,
                                $keepProduct->productStock->home_stock,
                                $keepProduct->productStock->store_stock,
                                $keepProduct->productStock->pre_order_stock,
                            );
                        }

                        // Restore store_stock
                        if ($keepProduct->store_stock > 0) {
                            $keepProduct->productStock->update([
                                'all_stock' => $keepProduct->productStock->all_stock + $keepProduct->store_stock,
                                'store_stock' => $keepProduct->productStock->store_stock + $keepProduct->store_stock,
                            ]);

                            setStockHistory(
                                $keepProduct->productStock->id,
                                StockActivity::KEEP,
                                StockStatus::REMOVE,
                                StockType::STORE_STOCK,
                                NULL,
                                $keepProduct->store_stock,
                                $keep->no_keep,
                                $keepProduct->productStock->all_stock,
                                $keepProduct->productStock->home_stock,
                                $keepProduct->productStock->store_stock,
                                $keepProduct->productStock->pre_order_stock,
                            );
                        }
                    }
                }

                $keep->update([
                    'status' => strtolower(KeepStatus::CANCELED),
                ]);
            }
        } catch (\Throwable $th) {
            $this->info($th->getMessage());
        }
    }
}
