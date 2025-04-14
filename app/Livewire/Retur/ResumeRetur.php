<?php

namespace App\Livewire\Retur;

use App\Enums\KeepStatus;
use App\Enums\ReturStatus;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ResumeRetur extends Component
{
    use WithPagination;

    public $returStatus, $retur_status;
    public $productData, $productLabel;
    public $perPage = 10;

    public function mount()
    {
        $this->returStatus = ReturStatus::asSelectArray();
        $this->updatedReturStatus();
    }

    public function render()
    {
        return view('livewire.retur.resume-retur', [
            'returItems' => DB::table('retur_items')
                ->join('product_stocks', 'retur_items.product_stock_id', '=', 'product_stocks.id')
                ->join('products', 'product_stocks.product_id', '=', 'products.id')
                ->join('colors', 'product_stocks.color_id', '=', 'colors.id')
                ->join('sizes', 'product_stocks.size_id', '=', 'sizes.id')
                ->join('returs', 'retur_items.retur_id', '=', 'returs.id')
                ->where('returs.status', 'like', '%' . $this->retur_status . '%')
                ->select(
                    'retur_items.product_stock_id',
                    'products.name as name',
                    'colors.name as color',
                    'sizes.name as size',
                    'retur_items.status as status',
                    'retur_items.price as price',
                    'retur_items.total_price as total_price',
                    DB::raw('SUM(retur_items.total_items) as items')
                )
                ->groupByRaw("
                    retur_items.product_stock_id,
                    products.name,
                    colors.name,
                    sizes.name,
                    retur_items.status,
                    retur_items.price,
                    retur_items.total_price,
                    CONCAT(products.name, ' ', colors.name, ' ', sizes.name)
                ")
                ->paginate($this->perPage,  ['*'], 'resumeRetur')
        ]);

    }

    public function updatedReturStatus()
    {
        $data = DB::table('retur_items')
            ->join('product_stocks', 'retur_items.product_stock_id', '=', 'product_stocks.id')
            ->join('products', 'product_stocks.product_id', '=', 'products.id')
            ->join('colors', 'product_stocks.color_id', '=', 'colors.id')
            ->join('sizes', 'product_stocks.size_id', '=', 'sizes.id')
            ->join('returs', 'retur_items.retur_id', '=', 'returs.id')
            ->where('returs.status', 'like', '%' . $this->retur_status . '%')
            ->select(
                'retur_items.product_stock_id',
                'products.name as name',
                'colors.name as color',
                'sizes.name as size',
                DB::raw("CONCAT(products.name, ' ', colors.name, ' ', sizes.name) as full_name"),
                DB::raw('SUM(retur_items.total_items) as items')
            )
            ->groupByRaw("retur_items.product_stock_id, products.name, colors.name, sizes.name, CONCAT(products.name, ' ', colors.name, ' ', sizes.name)")
            ->get();



    $this->productData = $data->pluck('items');
    $this->productLabel = $data->pluck('full_name');
    $this->dispatch('update-chart-product', [
        'productLabel' => $data->pluck('full_name'),
        'productData' => $data->pluck('items'),
    ]);
    }
}
