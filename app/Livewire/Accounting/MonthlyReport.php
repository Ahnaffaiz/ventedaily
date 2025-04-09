<?php

namespace App\Livewire\Accounting;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\Setting;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class MonthlyReport extends Component
{

    #[Rule('required')]
    public $month;
    public $salesCategories, $salesProducts;
    public $report, $setting;

    public $salesCategoriesChart;

    #[Title('Monthly Report')]

    public function mount()
    {
        $this->setting = Setting::first();
    }
    public function render()
    {
        return view('livewire.accounting.monthly-report');
    }

    public function generateReport()
    {
        $this->validate();
        [$year, $month] = explode('-', $this->month);

        $this->report['omzet'] = Sale::whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->sum('total_price');
        $this->report['net_profit'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('product_stocks', 'sale_items.product_stock_id', '=', 'product_stocks.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->select(DB::raw('SUM((sale_items.price - product_stocks.purchase_price) * sale_items.total_items) as total_profit'))
                            ->value('total_profit');
        $this->report['total_sales'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->select(DB::raw('SUM(sale_items.total_items) as total_sales'))
                            ->value('total_sales');
        $this->report['total_sales_reseller'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('customers', 'sales.customer_id', '=', 'customers.id')
                            ->join('groups', 'customers.group_id', '=', 'groups.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->where('groups.name', 'reseller')
                            ->select(DB::raw('SUM(sale_items.total_items) as total_sales'))
                            ->value('total_sales_reseller');
        $this->report['total_sales_shopee'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('marketplaces', 'sales.marketplace_id', '=', 'marketplaces.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->where('marketplaces.name', 'shopee')
                            ->select(DB::raw('SUM(sale_items.total_items) as total_sales'))
                            ->value('total_sales_shopee');
        $this->report['total_sales_tiktok'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('marketplaces', 'sales.marketplace_id', '=', 'marketplaces.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->where('marketplaces.name', 'tiktok')
                            ->select(DB::raw('SUM(sale_items.total_items) as total_sales'))
                            ->value('total_sales_tiktok');
        $this->report['total_sales_website'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('marketplaces', 'sales.marketplace_id', '=', 'marketplaces.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->where('marketplaces.name', 'website')
                            ->select(DB::raw('SUM(sale_items.total_items) as total_sales'))
                            ->value('total_sales_website');
        $this->report['monthly_cost'] = Expense::whereMonth('created_at', $month)
                            ->whereYear('created_at', $year)
                            ->sum('total_amount');
        $this->salesCategories = DB::table('sale_items')
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
            $this->salesProducts = DB::table('sale_items')
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
            $this->dispatch('categories-sold-loaded', [
                'categoryLabels' => $this->salesCategories->pluck('name')->values()->all(),
                'categoryData' => $this->salesCategories->pluck('total_items')->values()->all(),
                'productLabels' => $this->salesProducts->pluck('name')->values()->all(),
                'productData' => $this->salesProducts->pluck('total_items')->values()->all(),
            ]);
    }
}
