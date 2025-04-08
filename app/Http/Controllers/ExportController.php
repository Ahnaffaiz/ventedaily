<?php

namespace App\Http\Controllers;

use App\Models\ProductStock;
use App\Models\ProductStockHistory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ExportController extends Controller
{
    public function stockHistory($productStockId, $startDate, $endDate)
    {
        $start_date = Carbon::parse($startDate)->startOfDay();
        $end_date = Carbon::parse($endDate)->endOfDay();
        $productStock = ProductStock::where('id', $productStockId)->first();
        $stockHistories = ProductStockHistory::with('productStock')->whereBetween('created_at', [$start_date, $end_date])
            ->where('product_stock_id', $productStockId)
            ->orderBy('id', 'desc')
            ->get();
        $setting = Setting::first();

        return view('print.stock-history', compact('stockHistories', 'setting', 'productStock', 'startDate', 'endDate'));
    }
}
