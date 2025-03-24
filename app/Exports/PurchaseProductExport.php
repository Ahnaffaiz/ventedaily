<?php

namespace App\Exports;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class PurchaseProductExport implements FromView
{
    protected $purchaseItems, $start_date, $end_date, $setting;

    public function __construct($start_date, $end_date)
    {
        $this->setting = Setting::first();
        $this->start_date = Carbon::parse($start_date)->format('d/m/Y');
        $this->end_date = Carbon::parse($end_date)->format('d/m/Y');
        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();
        $purchases = Purchase::whereBetween('created_at', [$start_date, $end_date])->get();
        $this->purchaseItems = PurchaseItem::whereIn('purchase_id', $purchases->pluck('id'))
            ->with([
                'productStock.product',
                'productStock.color',
                'productStock.size'
            ])
            ->get()
            ->groupBy('product_stock_id')
            ->map(function ($items) use ($purchases) {
                $purchase = $purchases->firstWhere('id', $items->first()->purchase_id);
                return [
                    'product_stock_id' => $items->first()->product_stock_id,
                    'date' => Carbon::parse(optional($purchase)->created_at)->format('d/m/Y'),
                    'product_name' => $items->first()->productStock->product->name,
                    'color' => $items->first()->productStock->color->name,
                    'size' => $items->first()->productStock->size->name,
                    'qty' => $items->sum('total_items'),
                ];
            })->values();
    }

    public function view(): View
    {
        return view('export.excel.purchase-product', [
            'products' => $this->purchaseItems,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'setting' => $this->setting
        ]);
    }
}
