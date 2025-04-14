<?php

namespace App\Livewire\Keep;

use App\Enums\KeepStatus;
use App\Models\Group;
use App\Models\KeepProduct;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ResumeKeep extends Component
{
    use WithPagination;

    public $groups, $group_id;
    public $productData, $productLabel;
    public $perPage = 10;

    public function mount()
    {
        $this->groups = Group::get()->pluck('name', 'id');
        $this->updatedGroupId();
    }

    public function render()
    {
         // Kirim ke view
        return view('livewire.keep.resume-keep', [
            'keepProducts' => DB::table('keep_products')
                ->join('product_stocks', 'keep_products.product_stock_id', '=', 'product_stocks.id')
                ->join('products', 'product_stocks.product_id', '=', 'products.id')
                ->join('colors', 'product_stocks.color_id', '=', 'colors.id')
                ->join('sizes', 'product_stocks.size_id', '=', 'sizes.id')
                ->join('keeps', 'keep_products.keep_id', '=', 'keeps.id')
                ->join('customers', 'keeps.customer_id', '=', 'customers.id')
                ->where('customers.group_id', 'like', '%' . $this->group_id . '%')
                ->where('keeps.status', KeepStatus::ACTIVE)
                ->select(
                    'keep_products.product_stock_id',
                    'products.name as name',
                    'colors.name as color',
                    'sizes.name as size',
                    DB::raw('SUM(keep_products.total_items) as items')
                )
                ->groupBy('keep_products.product_stock_id', 'products.name')
                ->paginate($this->perPage,  ['*'], 'resumeKeep')
        ]);
    }

    public function updatedGroupId()
    {
        $data = DB::table('keep_products')
            ->join('product_stocks', 'keep_products.product_stock_id', '=', 'product_stocks.id')
            ->join('products', 'product_stocks.product_id', '=', 'products.id')
            ->join('colors', 'product_stocks.color_id', '=', 'colors.id')
            ->join('sizes', 'product_stocks.size_id', '=', 'sizes.id')
            ->join('keeps', 'keep_products.keep_id', '=', 'keeps.id')
            ->join('customers', 'keeps.customer_id', '=', 'customers.id')
            ->where('customers.group_id', 'like', '%' . $this->group_id . '%')
            ->where('keeps.status', KeepStatus::ACTIVE)
            ->select(
                'keep_products.product_stock_id',
                'products.name as name',
                'colors.name as color',
                'sizes.name as size',
                DB::raw("CONCAT(products.name, ' ', colors.name, ' ', sizes.name) as full_name"),
                DB::raw('SUM(keep_products.total_items) as items')
            )
            ->groupBy(
                'keep_products.product_stock_id',
                'products.name',
                'colors.name',
                'sizes.name'
            )
            ->get();


    $this->productData = $data->pluck('items');
    $this->productLabel = $data->pluck('full_name');
    $this->dispatch('update-chart-product', [
        'productLabel' => $data->pluck('full_name'),
        'productData' => $data->pluck('items'),
    ]);
    }
}
