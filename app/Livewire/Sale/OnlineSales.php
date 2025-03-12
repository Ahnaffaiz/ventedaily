<?php

namespace App\Livewire\Sale;

use App\Models\Sale;
use Livewire\Attributes\Title;
use Livewire\Component;

class OnlineSales extends Component
{
    public $sale;
    public $isOpen = false;
    public $query = '', $perPage = 10, $sortBy = 'no_sale', $sortDirection = 'desc';

    #[Title('Online Sales')]

    public $showColumns = [
        'no_keep' => false,
        'total_items' => true,
        'total_price' => true,
        'ship_status' => true,
        'created_at' => false,
        'updated_at' => false,
    ];

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
        return view('livewire.sale.online-sales', [
                'onlineSales' => Sale::whereHas('customer', function($query){
                    return $query->where('group_id', 2);
                })
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->where('no_sale', 'like', '%' . $this->query . '%')
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
            ]);
    }
}
