<?php

namespace App\Exports;

use App\Models\Purchase;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchaseExport implements FromView
{
    protected $start_date, $end_date, $purchases;
    public function __construct($start_date, $end_date)
    {
        $this->start_date = Carbon::parse($start_date)->format('d/m/Y');
        $this->end_date = Carbon::parse($end_date)->format('d/m/Y');
        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();
        $this->purchases = Purchase::whereBetween('created_at', [$start_date, $end_date])->get();
    }

    public function view() : View
    {
        return view('export.excel.purchase', [
            'purchases' => $this->purchases,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'setting' => Setting::first(),
        ]);
    }
}
