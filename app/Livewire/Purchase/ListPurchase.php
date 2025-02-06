<?php

namespace App\Livewire\Purchase;

use App\Models\Purchase;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ListPurchase extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $purchase;
    public $query = '', $perPage = 10, $sortBy = 'name', $sortDirection = 'asc';
    public $showColumns = [
        'supplier_id' => true,
        'term_of_payment_id' => true,
        'sub_total' => true,
        'discount' => false,
        'tax' => true,
        'total_price' => true,
        'outstanding_balance' => true,
        'created_at' => false,
        'updated_at' => false,
    ];

    #[Title('Purchase')]

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
        return view('livewire.purchase.list-purchase', [
            'purchases' => Purchase::select('purchases.*')
                ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                ->where('suppliers.name', 'like', '%' . $this->query . '%')
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
