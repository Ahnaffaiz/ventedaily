<?php

namespace App\Exports;

use App\Enums\KeepStatus;
use App\Models\KeepProduct;
use App\Models\Setting;
use App\Models\TransferStock;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class TransferStockExport implements FromView
{
    protected $transferStock;

    public function __construct($transferStockId)
    {
        $this->transferStock = TransferStock::where('id', $transferStockId)->first();

    }

    public function view(): View
    {
        return view('export.excel.transfer-stock', [
            'transferStock' => $this->transferStock,
            'setting' => Setting::first()
        ]);
    }
}
