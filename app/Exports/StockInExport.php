<?php

namespace App\Exports;

use App\Models\Setting;
use App\Models\StockIn;
use App\Models\TransferStock;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StockInExport implements FromView
{
    protected $stockIns, $start_date, $end_date, $setting, $stockType;

    public function __construct($start_date, $end_date, $stockType = null)
    {
        $this->setting = Setting::first();
        $this->stockType = $stockType;
        $this->start_date = Carbon::parse($start_date)->format('d/m/Y');
        $this->end_date = Carbon::parse($end_date)->format('d/m/Y');
        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();
        if($stockType != null) {
            $this->stockIns = StockIn::where('stock_type', $this->stockType)
                ->whereBetween('created_at', [$start_date, $end_date])->get();
        } else {
            $this->stockIns = StockIn::whereBetween('created_at', [$start_date, $end_date])->get();
        }
    }

    public function view() : View
    {
        return view('export.excel.stock-in', [
            'stockIns' => $this->stockIns,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'setting' => Setting::first(),
            'stockType' => $this->stockType,
        ]);
    }
}
