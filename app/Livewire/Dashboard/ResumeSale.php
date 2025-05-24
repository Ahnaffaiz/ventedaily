<?php

namespace App\Livewire\Dashboard;

use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ResumeSale extends Component
{
    use WithPagination;

    public $groups, $group_id;
    public $productData, $productLabel;
    public $perPage = 10;
    public $perPageOptions = [10, 50, 100, 200];

    public function mount()
    {
        $this->groups = Group::get()->pluck('name', 'id');
        $this->updatedGroupId();
    }

    public function render()
    {
         // Kirim ke view
        return view('livewire.dashboard.resume-sale', [
            'saleItems' => DB::table('sale_items')
                ->join('product_stocks', 'sale_items.product_stock_id', '=', 'product_stocks.id')
                ->join('products', 'product_stocks.product_id', '=', 'products.id')
                ->join('colors', 'product_stocks.color_id', '=', 'colors.id')
                ->join('sizes', 'product_stocks.size_id', '=', 'sizes.id')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->where('customers.group_id', 'like', '%' . $this->group_id . '%')
                ->whereDate('sales.created_at', Carbon::today())
                ->select(
                    'sale_items.product_stock_id',
                    'products.name as name',
                    'colors.name as color',
                    'sizes.name as size',
                    DB::raw('SUM(sale_items.total_items) as items')
                )
                ->groupByRaw("sale_items.product_stock_id, products.name, colors.name, sizes.name, CONCAT(products.name, ' ', colors.name, ' ', sizes.name)")
                ->orderBy('items', 'desc')
                ->paginate($this->perPage,  ['*'], 'resumeSale')
        ]);
    }

    public function updatedGroupId()
    {
        $data = DB::table('sale_items')
            ->join('product_stocks', 'sale_items.product_stock_id', '=', 'product_stocks.id')
            ->join('products', 'product_stocks.product_id', '=', 'products.id')
            ->join('colors', 'product_stocks.color_id', '=', 'colors.id')
            ->join('sizes', 'product_stocks.size_id', '=', 'sizes.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->where('customers.group_id', 'like', '%' . $this->group_id . '%')
            ->whereDate('sales.created_at', Carbon::today())
            ->select(
                'sale_items.product_stock_id',
                'products.name as name',
                'colors.name as color',
                'sizes.name as size',
                DB::raw("CONCAT(products.name, ' ', colors.name, ' ', sizes.name) as full_name"),
                DB::raw('SUM(sale_items.total_items) as items')
            )
            ->groupByRaw("sale_items.product_stock_id, products.name, colors.name, sizes.name, CONCAT(products.name, ' ', colors.name, ' ', sizes.name)")
            ->orderBy('items', 'desc')
            ->limit(20)
            ->get();



    $this->productData = $data->pluck('items');
    $this->productLabel = $data->pluck('full_name');
    $this->dispatch('update-chart-product', [
        'productLabel' => $data->pluck('full_name'),
        'productData' => $data->pluck('items'),
    ]);
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
}
