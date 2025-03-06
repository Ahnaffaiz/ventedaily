<?php

namespace App\Console\Commands;

use App\Enums\KeepStatus;
use App\Models\Keep;
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
        $keep = Keep::where('keep_time', '<', Carbon::now())
            ->where('status', strtolower(KeepStatus::ACTIVE))
            ->update(['status' => strtolower(KeepStatus::CANCELED)]);

        $this->info(' Status keep telah diperbarui.');
    }
}
