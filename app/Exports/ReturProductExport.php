<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Group;
use App\Models\Retur;
use App\Models\ReturItem;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class ReturProductExport implements FromView
{
    protected $returItems, $start_date, $end_date, $setting, $returs;

    public function __construct($start_date, $end_date)
    {
        $this->setting = Setting::first();
        $this->start_date = Carbon::parse($start_date)->format('d/m/Y');
        $this->end_date = Carbon::parse($end_date)->format('d/m/Y');
        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();
        $this->returs = Retur::whereBetween('created_at', [$start_date, $end_date])->get();
        $retur = $this->returs;
        $this->returItems = ReturItem::whereIn('retur_id', $this->returs->pluck('id'))
            ->with([
                'productStock.product',
                'productStock.color',
                'productStock.size'
            ])
            ->get()
            ->groupBy('product_stock_id')
            ->map(function ($items) use ( $retur) {
                $retur = $this->returs->firstWhere('id', $items->first()->retur_id);
                return [
                    'product_stock_id' => $items->first()->product_stock_id,
                    'date' => Carbon::parse(optional($retur)->created_at)->format('d/m/Y'),
                    'product_name' => $items->first()->productStock->product->name,
                    'color' => $items->first()->productStock->color->name,
                    'size' => $items->first()->productStock->size->name,
                    'qty' => $items->sum('total_items'),
                ];
            })->values();
    }

    public function view(): View
    {
        return view('export.excel.retur-product', [
            'returs' => $this->returs,
            'products' => $this->returItems,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'setting' => $this->setting,
        ]);
    }
}
