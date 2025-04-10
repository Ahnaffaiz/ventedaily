<?php

namespace App\Livewire\Accounting;

use App\Exports\SalesReportExport;
use Carbon\Carbon;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class SalesReport extends Component
{
    #[Title('Sales Report')]

    #[Rule('required')]
    public $start_date, $end_date;
    public function render()
    {
        return view('livewire.accounting.sales-report');
    }

    public function generateReport()
    {
        $this->validate();
        $name = "Sales Report Accouting " . Carbon::parse($this->start_date)->translatedFormat('d F Y') ." - ". Carbon::parse($this->end_date)->translatedFormat('d F Y') .".xlsx";
        return Excel::download(new SalesReportExport($this->start_date, $this->end_date), $name);
    }
}
