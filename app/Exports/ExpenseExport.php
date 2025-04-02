<?php

namespace App\Exports;

use App\Models\Cost;
use App\Models\Expense;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromView;

class ExpenseExport implements FromView
{
    protected $start_date, $end_date, $cost_id, $expenses, $cost;

    public function __construct($start_date, $end_date, $cost_id = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->cost_id = $cost_id;
        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();
        if($this->cost_id) {
            $this->cost = Cost::where('id', $this->cost_id)->first();
            $this->expenses = Expense::whereBetween('date', [$start_date, $end_date])->where('cost_id', $this->cost_id)->get();
        } else {
            $this->expenses = Expense::whereBetween('date', [$start_date, $end_date])->get();
        }
    }

    public function view() : View
    {
        return view('export.excel.expense', [
            'expenses' => $this->expenses,
            'setting' => Setting::first(),
            'start_date' => $this->end_date,
            'end_date' => $this->end_date,
            'cost' => $this->cost
        ]);
    }
}
