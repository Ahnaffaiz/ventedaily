<?php

namespace App\Livewire\Sale;

use App\Enums\KeepStatus;
use App\Models\Keep;
use App\Models\Sale;
use Carbon\Carbon;
use Exception;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ListSale extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    public $sale;
    public $isOpen = false, $isPayment = false;
    public $query = '', $perPage = 10, $sortBy = 'name', $sortDirection = 'asc';
    public $showColumns = [
        'no_sale' => true,
        'customer_id' => true,
        'term_of_payment_id' => true,
        'total_items' => true,
        'sub_total' => true,
        'discount' => true,
        'tax' => false,
        'total_price' => true,
        'payment_type' => true,
        'created_at' => false,
        'updated_at' => false,
    ];

    protected $listeners = [
        'delete'
    ];

    #[Title('Sale')]

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
        return view('livewire.sale.list-sale', [
            'sales' => Sale::select('sales.*')
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->where('customers.name', 'like', '%' . $this->query . '%')
                ->orWhere('no_sale', 'like', '%' . $this->query . '%')
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function addPayment($sale)
    {
        $this->isPayment = true;
        $this->sale = Sale::with('purchasePayments')->where('id', $sale)->first();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->reset();
        $this->isOpen = false;
    }

    public function deleteAlert($sale)
    {
        $this->sale = Sale::find($sale);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete Sale ?',
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
        $stockStype = $this->sale->customer->group_id == 1 ? 'store_stock' : 'home_stock';
        try {
            foreach ($this->sale->saleItems as $saleItem) {
                if(!$this->sale->keep()->exists()){
                    $saleItem->productStock->update([
                        'all_stock' => $saleItem->productStock->all_stock + $saleItem->total_items,
                        $stockStype => $saleItem->productStock->$stockStype + $saleItem->total_items,
                    ]);
                }
            }
            if($this->sale->keep_id != null) {
                if($this->sale->keep->keep_time >= Carbon::now()) {
                    $this->sale->keep->update([
                        'status' => KeepStatus::ACTIVE
                    ]);
                } else {
                    $this->sale->keep->update([
                        'status' => KeepStatus::CANCELED
                    ]);
                }
            }
            $this->sale->delete();
            $this->alert('success', 'Sale Succesfully Deleted');
        } catch (Exception $th) {
            $this->alert('error', $th);
        }
    }
}