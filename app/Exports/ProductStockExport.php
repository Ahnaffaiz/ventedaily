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
        $this->productStocks = ProductStock::with(['product', 'color', 'size'])
            ->join('products', 'product_stocks.product_id', '=', 'products.id')
            ->join('colors', 'product_stocks.color_id', '=', 'colors.id')
            ->join('sizes', 'product_stocks.size_id', '=', 'sizes.id')
            ->select('product_stocks.*')
            ->orderBy('products.name')
            ->orderBy('colors.name')
            ->orderBy('sizes.name')
            ->get();
    }

    public function view(): View
    {
        return view('export.excel.product-stock', [
            'setting' => $this->setting,
            'productStocks' => $this->productStocks
        ]);
    }
}
