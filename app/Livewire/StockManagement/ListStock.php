<?php

namespace App\Livewire\StockManagement;

use App\Models\Category;
use App\Models\ProductStock;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ListStock extends Component
{
    use WithPagination;

    public $query = '', $perPage = 10, $sortBy = 'products.name', $sortDirection = 'asc';
    public $showColumns = [
        'code' => false,
        'category_id' => true,
        'status' => true,
        'size' => true,
        'color' => true,
        'all_stock' => true,
        'home_stock' => true,
        'store_stock' => true,
        'created_at' => false,
        'updated_at' => false,
    ];

    #[Title('Product Stock')]

    protected $listeners = [
        'delete'
    ];

    public function updatedQuery()
    {
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $column;
    }

    public function updatedShowColumns($column)
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.stock-management.list-stock', [
            'productStocks' => ProductStock::with('size', 'color')->select('product_stocks.*')
                ->join('products', 'product_id', '=', 'products.id')
                ->join('sizes', 'size_id', '=', 'sizes.id')
                ->join('colors', 'color_id', '=', 'colors.id')
                ->where('products.name', 'like', '%' . $this->query . '%')
                ->orWhere('colors.name','like', '%' . $this->query . '%')
                ->orWhere('sizes.name','like', '%' . $this->query . '%')
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
