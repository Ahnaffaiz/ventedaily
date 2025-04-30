<?php

namespace App\Exports;

use App\Models\ProductStock;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductStockExport implements FromView
{
    protected $setting;
    protected $productStocks;

    public function __construct()
    {
        $this->setting = Setting::first();
        $this->productStocks = ProductStock::with(['product', 'color', 'size'])->get();
    }

    public function view(): View
    {
        return view('export.excel.product-stock', [
            'setting' => $this->setting,
            'productStocks' => $this->productStocks
        ]);
    }
}
