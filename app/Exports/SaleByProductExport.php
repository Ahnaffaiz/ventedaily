<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SaleByProductExport implements FromView
{
    protected $saleItems, $start_date, $end_date, $setting, $product_id, $product, $sales;

    public function __construct($start_date, $end_date, $product_id=null,)
    {
        $this->setting = Setting::first();
        $this->start_date = Carbon::parse($start_date)->format('d/m/Y');
        $this->end_date = Carbon::parse($end_date)->format('d/m/Y');
        $this->product_id = $product_id;
        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();
        $this->sales = Sale::whereBetween('created_at', [$start_date, $end_date])->get();
        if ($this->product_id) {
            $this->product = Product::find($this->product_id);
            $this->sales = Sale::whereBetween('created_at', [$start_date, $end_date])
                    ->whereHas('saleItems', function($query){
                        $query->whereHas('productStock', function($query) {
                            $query->where('product_id', $this->product_id);
                        });
                    })->get();
            $saleItems = SaleItem::whereHas('productStock', function($query){
                    return $query->where('product_id', $this->product_id);
                })->whereIn('sale_id', $this->sales->pluck('id'))
                    ->with([
                        'productStock.product',
                        'productStock.color',
                        'productStock.size'
                    ])
                    ->get();
        } else {
            $this->sales = Sale::whereBetween('created_at', [$start_date, $end_date])
                ->get();
            $saleItems = SaleItem::whereIn('sale_id', $this->sales->pluck('id'))
                ->with([
                    'productStock.product',
                    'productStock.color',
                    'productStock.size'
                ])
                ->get();
        }
        $sale = $this->sales;

        $this->saleItems = $saleItems->groupBy('product_stock_id')
            ->map(function ($items) use ( $sale) {
                $sale = $this->sales->firstWhere('id', $items->first()->sale_id);
                return [
                    'product_stock_id' => $items->first()->product_stock_id,
                    'date' => Carbon::parse(optional($sale)->created_at)->format('d/m/Y'),
                    'product_name' => $items->first()->productStock->product->name,
                    'color' => $items->first()->productStock->color->name,
                    'size' => $items->first()->productStock->size->name,
                    'qty' => $items->sum('total_items'),
                    'net_sale' => $items->sum('total_price')
                ];
            })
            ->sortBy([
                ['product_name', 'asc'],
                ['color', 'asc'],
                ['size', 'asc']
            ])
            ->values();
    }

    public function view(): View
    {
        return view('export.excel.sale-by-product', [
            'sales' => $this->sales,
            'products' => $this->saleItems,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'setting' => $this->setting,
            'product' => $this->product,
        ]);
    }
}
