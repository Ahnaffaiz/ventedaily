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
                foreach ($keep?->keepProducts as $keepProduct) {
                    $stockType = $keep->customer->group->name == 'Reseller' ? 'store_stock' : 'home_stock';
                    $keepProduct->productStock->update([
                        'all_stock' => $keepProduct->productStock->all_stock + $keepProduct->total_items,
                        $stockType => $keepProduct->productStock->$stockType + $keepProduct->total_items,
                    ]);
                    setStockHistory(
                        $keepProduct->productStock->id,
                        StockActivity::KEEP,
                        StockStatus::REMOVE,
                        StockType::HOME_STOCK,
                        $stockType,
                        $keepProduct->total_items,
                        $keep->no_keep,
                        $keepProduct->productStock->all_stock,
                        $keepProduct->productStock->home_stock,
                        $keepProduct->productStock->store_stock,
                        $keepProduct->productStock->pre_order_stock,
                    );
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
