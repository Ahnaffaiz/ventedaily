<?php

namespace App\Livewire\PreOrder;

use App\Enums\StockActivity;
use App\Enums\StockStatus;
use App\Enums\StockType;
use App\Models\PreOrder;
use App\Models\Purchase;
use Exception;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ListPreOrder extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    public $isOpen = false;
    public $preOrder;
    public $query = '', $perPage = 10, $sortBy = 'no_pre_order', $sortDirection = 'desc';
    public $perPageOptions = [10, 25, 50, 100];
    public $total_price;
    public $showColumns = [
        'total_items' => true,
        'total_price' => true,
        'created_at' => false,
        'updated_at' => false,
    ];

    protected $listeners = [
        'delete'
    ];

    #[Title('PreOrder')]

    public function closeModal()
    {
        $this->isOpen = false;
        $this->preOrder = null;
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

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedShowColumns($column)
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.pre-order.list-pre-order', [
            'preOrders' => PreOrder::select('pre_orders.*')
                ->join('customers', 'pre_orders.customer_id', '=', 'customers.id')
                ->where('customers.name', 'like', '%' . $this->query . '%')
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function show($pre_order_id) {
        $this->isOpen = true;
        $this->preOrder = PreOrder::find($pre_order_id);
        $this->total_price = array_sum(array_column($this->preOrder->preOrderProducts->toArray(), 'total_price'));
    }

    public function deleteAlert($preOrder)
    {
        $this->preOrder = PreOrder::find($preOrder);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete this preOrder ?',
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Yes',
            'showCancelButton' => true,
            'cancelButtonText' => 'Cancel',
            'icon' => 'warning',
            'onConfirmed' => 'delete',
            'timer' => null,
            'confirmButtonColor' => '#3085d6',
            'cancelButtonColor' => '#d33',
            'customClass' => [
                'confirmButton' => 'btn bg-primary text-white hover:bg-primary-dark',
                'cancelButton' => 'btn bg-danger text-white hover:bg-danger-dark'
            ]
        ]);
    }

    public function delete()
    {
        try {
            foreach ($this->preOrder->preOrderProducts as $preOrderProduct) {
                $preOrderProduct->productStock->update([
                    'all_stock' => $preOrderProduct->productStock->all_stock + $preOrderProduct->total_items,
                    'pre_order_stock' => $preOrderProduct->productStock->pre_order_stock + $preOrderProduct->total_items,
                ]);
                setStockHistory(
                    $preOrderProduct->productStock->id,
                    StockActivity::PRE_ORDER,
                    StockStatus::REMOVE,
                    StockType::PRE_ORDER_STOCK,
                    NULL,
                    $preOrderProduct->total_items,
                    $this->preOrder->no_pre_order,
                    $preOrderProduct->productStock->all_stock,
                    $preOrderProduct->productStock->home_stock,
                    $preOrderProduct->productStock->store_stock,
                    $preOrderProduct->productStock->pre_order_stock,
                );
            }
            $this->preOrder->delete();
            $this->alert('success', 'PreOrder Data Succesfully Deleted');
        } catch (Exception $th) {
            $this->alert('error', $th);
        }
    }
}
