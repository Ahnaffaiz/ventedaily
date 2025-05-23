<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Group;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class SaleProductExport implements FromView
{
    protected $saleItems, $start_date, $end_date, $setting, $group_id, $customer_id, $group, $customer, $sales;

    public function __construct($start_date, $end_date, $group_id=null, $customer_id=null)
    {
        $this->setting = Setting::first();
        $this->start_date = Carbon::parse($start_date)->format('d/m/Y');
        $this->end_date = Carbon::parse($end_date)->format('d/m/Y');
        $this->group_id = $group_id;
        $this->customer_id = $customer_id;
        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();
        $this->sales = Sale::whereBetween('created_at', [$start_date, $end_date])->get();
        if ($this->group_id) {
            $this->group = Group::find($this->group_id);
            if ($this->customer_id) {
                $this->customer = Customer::find($this->customer_id);
                $this->sales = Sale::whereBetween('created_at', [$start_date, $end_date])
                    ->where('customer_id', $this->customer_id)
                    ->get();
            } else {
                $this->sales = Sale::whereBetween('created_at', [$start_date, $end_date])
                    ->whereHas('customer', function($query){
                        $query->where('group_id', $this->group_id);
                    })->get();
            }
        } else {
            $this->sales = Sale::whereBetween('created_at', [$start_date, $end_date])
                ->get();
        }
        $sale = $this->sales;
        $this->saleItems = SaleItem::whereIn('sale_id', $this->sales->pluck('id'))
            ->with([
                'productStock.product',
                'productStock.color',
                'productStock.size'
            ])
            ->get()
            ->groupBy('product_stock_id')
            ->map(function ($items) use ( $sale) {
                $sale = $this->sales->firstWhere('id', $items->first()->sale_id);
                return [
                    'product_stock_id' => $items->first()->product_stock_id,
                    'date' => Carbon::parse(optional($sale)->created_at)->format('d/m/Y'),
                    'product_name' => $items->first()->productStock->product->name,
                    'color' => $items->first()->productStock->color->name,
                    'size' => $items->first()->productStock->size->name,
                    'qty' => $items->sum('total_items'),
                ];
            })
            ->sortBy('product_name')
            ->values();
    }

    public function view(): View
    {
        return view('export.excel.sale-product', [
            'sales' => $this->sales,
            'products' => $this->saleItems,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'setting' => $this->setting,
            'group' => $this->group,
            'customer' => $this->customer
        ]);
    }
}
