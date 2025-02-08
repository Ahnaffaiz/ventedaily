<?php

namespace App\Livewire\Purchase;

use App\Models\Purchase;
use Exception;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ListPurchase extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    public $purchase;
    public $isOpen = false, $isPayment = false;
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

    protected $listeners = [
        'delete'
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

    public function addPayment($purchase)
    {
        $this->isPayment = true;
        $this->purchase = Purchase::with('purchasePayments')->where('id', $purchase)->first();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->reset();
        $this->isOpen = false;
    }

    public function deleteAlert($purchase)
    {
        $this->purchase = Purchase::find($purchase);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete Purchase ?',
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Yes',
            'showCancelButton' => true,
            'cancelButtonText' => 'cancel',
            'icon' => 'warning',
            'onConfirmed' => 'delete',
            'timer' => null,
            'confirmButtonColor' => '#3085d6',
            'cancelButtonColor' => '#d33'
        ]);
    }

    public function delete()
    {
        try {
            $this->purchase->delete();
            $this->alert('success', 'Purchase Succesfully Deleted');
        } catch (Exception $th) {
            $this->alert('error', $th);
        }
    }
}