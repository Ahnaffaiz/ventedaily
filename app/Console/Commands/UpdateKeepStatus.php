<?php

namespace App\Console\Commands;

use App\Enums\KeepStatus;
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
                    $productStock = ProductStock::find($keepProduct->product_stock_id);
                    $productStock->update([
                        'home_stock' => $productStock->home_stock + $keepProduct->home_stock,
                        'store_stock' => $productStock->store_stock + $keepProduct->store_stock,
                        'all_stock' => $productStock->all_stock + $keepProduct->home_stock + $keepProduct->store_stock,
                    ]);
                    $keepProduct->update([
                        'home_stock' => 0,
                        'store_stock' => 0,
                    ]);
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
