<?php

namespace App\Exports;

use App\Models\Setting;
use App\Models\TransferStock;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransferStockInExport implements FromView
{
    protected $transferStocks, $start_date, $end_date, $setting, $stockFrom, $stockTo;

    public function __construct($start_date, $end_date, $stockFrom = null, $stockTo = null)
    {
        $this->setting = Setting::first();
        $this->stockFrom = $stockFrom;
        $this->stockTo = $stockTo;
        $this->start_date = Carbon::parse($start_date)->format('d/m/Y');
        $this->end_date = Carbon::parse($end_date)->format('d/m/Y');
        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();
        if($stockFrom && $stockTo) {
            $this->transferStocks = TransferStock::where('transfer_from', $this->stockFrom)->where('transfer_to', $this->stockTo)
                ->whereBetween('created_at', [$start_date, $end_date])->get();
        } else {
            $this->transferStocks = TransferStock::whereBetween('created_at', [$start_date, $end_date])->get();
        }
    }

    public function view() : View
    {
        return view('export.excel.transfer-stock-in', [
            'transferStocks' => $this->transferStocks,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'setting' => Setting::first(),
            'stockFrom' => $this->stockFrom,
            'stockTo' => $this->stockTo,
        ]);
    }
}
