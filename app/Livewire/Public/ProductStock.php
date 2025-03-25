<?php

namespace App\Livewire\Public;

use App\Models\ProductStock as ModelsProductStock;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ProductStock extends Component
{
    use WithPagination;

    public $query ='', $perPage = 10;

    #[Title('Product Stock')]
    public function updatedQuery()
    {
        $this->resetPage();
    }
    public function render()
    {
        return view('livewire.public.product-stock', [
            'productStocks' => ModelsProductStock::whereHas('product', function($query) {
                $query->where('name', 'like', '%'.$this->query.'%');
            })->paginate($this->perPage)
        ]);
    }
}
