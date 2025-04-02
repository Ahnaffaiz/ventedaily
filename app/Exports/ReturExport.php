<?php

namespace App\Exports;

use App\Enums\DiscountType;
use App\Models\Retur;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReturExport implements FromView
{
    protected $start_date, $end_date, $returs;
    protected $total_price;
    public function __construct($start_date, $end_date)
    {
        $this->start_date = Carbon::parse($start_date)->format('d/m/Y');
        $this->end_date = Carbon::parse($end_date)->format('d/m/Y');
        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();
        $this->returs = Retur::whereBetween('created_at', [$start_date, $end_date])->get();

        $this->total_price = $this->returs->sum('total_price');
    }

    public function view() : View
    {
        return view('export.excel.retur', [
            'returs' => $this->returs,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'setting' => Setting::first(),
            'total_price' => $this->total_price,
        ]);
    }
}
