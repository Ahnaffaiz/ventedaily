<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchaseByProductExport implements FromView
{
    protected $purchaseItems, $start_date, $end_date, $setting, $product_id, $product, $purchases;

    public function __construct($start_date, $end_date, $product_id=null,)
    {
        $this->setting = Setting::first();
        $this->start_date = Carbon::parse($start_date)->format('d/m/Y');
        $this->end_date = Carbon::parse($end_date)->format('d/m/Y');
        $this->product_id = $product_id;
        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();
        $this->purchases = Purchase::whereBetween('created_at', [$start_date, $end_date])->get();
        if ($this->product_id) {
            $this->product = Product::find($this->product_id);
            $this->purchases = Purchase::whereBetween('created_at', [$start_date, $end_date])
                    ->whereHas('purchaseItems', function($query){
                        $query->whereHas('productStock', function($query) {
                            $query->where('product_id', $this->product_id);
                        });
                    })->get();
            $purchaseItem = PurchaseItem::whereHas('productStock', function($query){
                return $query->where('product_id', $this->product_id);
            })->whereIn('purchase_id', $this->purchases->pluck('id'))
                ->with([
                    'productStock.product',
                    'productStock.color',
                    'productStock.size'
                ])
                ->get();
        } else {
            $this->purchases = Purchase::whereBetween('created_at', [$start_date, $end_date])
                ->get();
            $purchaseItem = PurchaseItem::whereIn('purchase_id', $this->purchases->pluck('id'))
                ->with([
                    'productStock.product',
                    'productStock.color',
                    'productStock.size'
                ])
                ->get();
        }

        $purchase = $this->purchases;
        $this->purchaseItems = $purchaseItem->groupBy('product_stock_id')
            ->map(function ($items) use ( $purchase) {
                $purchase = $this->purchases->firstWhere('id', $items->first()->purchase_id);
                return [
                    'product_stock_id' => $items->first()->product_stock_id,
                    'date' => Carbon::parse(optional($purchase)->created_at)->format('d/m/Y'),
                    'product_name' => $items->first()->productStock->product->name,
                    'color' => $items->first()->productStock->color->name,
                    'size' => $items->first()->productStock->size->name,
                    'qty' => $items->sum('total_items'),
                    'total_purchase' => $items->sum('total_price')
                ];
            })->values();
    }

    public function view(): View
    {
        return view('export.excel.purchase-by-product', [
            'purchases' => $this->purchases,
            'products' => $this->purchaseItems,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'setting' => $this->setting,
            'product' => $this->product,
        ]);
    }
}
