<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ProductStock;
use App\Models\ProductStockHistory;
use App\Models\Sale;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function monthlyReport($month)
    {
        $setting = Setting::first();
        $monthYear = $month;
        [$year, $month] = explode('-', $month);

        $report['omzet'] = Sale::whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->sum('total_price');
        $report['net_profit'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('product_stocks', 'sale_items.product_stock_id', '=', 'product_stocks.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->select(DB::raw('SUM((sale_items.price - product_stocks.purchase_price) * sale_items.total_items) as total_profit'))
                            ->value('total_profit');
        $report['total_sales'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->select(DB::raw('SUM(sale_items.total_items) as total_sales'))
                            ->value('total_sales');
        $report['total_sales_reseller'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('customers', 'sales.customer_id', '=', 'customers.id')
                            ->join('groups', 'customers.group_id', '=', 'groups.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->where('groups.name', 'reseller')
                            ->select(DB::raw('SUM(sale_items.total_items) as total_sales'))
                            ->value('total_sales_reseller');
        $report['total_sales_shopee'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('marketplaces', 'sales.marketplace_id', '=', 'marketplaces.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->where('marketplaces.name', 'shopee')
                            ->select(DB::raw('SUM(sale_items.total_items) as total_sales'))
                            ->value('total_sales_shopee');
        $report['total_sales_tiktok'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('marketplaces', 'sales.marketplace_id', '=', 'marketplaces.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->where('marketplaces.name', 'tiktok')
                            ->select(DB::raw('SUM(sale_items.total_items) as total_sales'))
                            ->value('total_sales_tiktok');
        $report['total_sales_website'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('marketplaces', 'sales.marketplace_id', '=', 'marketplaces.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->where('marketplaces.name', 'website')
                            ->select(DB::raw('SUM(sale_items.total_items) as total_sales'))
                            ->value('total_sales_website');
        $report['monthly_cost'] = Expense::whereMonth('created_at', $month)
                            ->whereYear('created_at', $year)
                            ->sum('total_amount');
        $salesCategories = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('product_stocks', 'sale_items.product_stock_id', '=', 'product_stocks.id')
                            ->join('products', 'product_stocks.product_id', '=', 'products.id')
                            ->join('categories', 'products.category_id', '=', 'categories.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->select(
                                'categories.id',
                                'categories.name',
                                DB::raw('SUM(sale_items.total_items) as total_items')
                            )
                            ->groupBy('categories.id', 'categories.name')
                            ->get();
            $salesProducts = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('product_stocks', 'sale_items.product_stock_id', '=', 'product_stocks.id')
                            ->join('products', 'product_stocks.product_id', '=', 'products.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->select(
                                'products.id',
                                'products.name',
                                DB::raw('SUM(sale_items.total_items) as total_items')
                            )
                            ->groupBy('products.id', 'products.name')
                            ->get();
        return view('print.monthly-report', compact('monthYear', 'report', 'salesCategories', 'salesProducts','setting'));
    }
}
