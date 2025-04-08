<?php

namespace App\Livewire\Product\TransferStock;

use App\Enums\StockActivity;
use App\Enums\StockStatus;
use App\Models\ProductStock;
use App\Models\TransferStock;
use Exception;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ListTransferStock extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    public $isOpen = false;
    public $transferStock;
    public $query = '', $perPage = 10, $sortBy = 'created_at', $sortDirection = 'desc';
    public $total_price;

    protected $listeners = [
        'delete'
    ];

    #[Title('Transfer Stock')]

    public function closeModal()
    {
        $this->isOpen = false;
        $this->transferStock = null;
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

    public function render()
    {
        return view('livewire.product.transfer-stock.list-transfer-stock', [
            'transferStocks' => TransferStock::select('transfer_stocks.*')
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function show($transfer_stock_id) {
        $this->isOpen = true;
        $this->transferStock = TransferStock::find($transfer_stock_id);
    }

    public function deleteAlert($transferStock)
    {
        $this->transferStock = TransferStock::find($transferStock);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete this Transfer Data ?',
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
            foreach ($this->transferStock->transferProducts as $transferProduct) {
                $productStock = ProductStock::where('id', $transferProduct->product_stock_id)->first();
                $productStock->update([
                    $this->transferStock->transfer_from => $productStock[$this->transferStock->transfer_from] + $transferProduct->stock,
                    $this->transferStock->transfer_to => $productStock[$this->transferStock->transfer_to] - $transferProduct->stock,
                ]);
                setStockHistory(
                    $productStock->id,
                    StockActivity::TRANSFER,
                    StockStatus::REMOVE,
                    $this->transferStock->transfer_to,
                    $this->transferStock->transfer_from,
                    $transferProduct->stock,
                    NULL,
                    $productStock->all_stock,
                    $productStock->home_stock,
                    $productStock->store_stock,
                    $productStock->pre_order_stock,
                );

                $transferProduct->delete();
            }
            $this->transferStock->delete();
            $this->alert('success', 'Transfer Data Succesfully Deleted');
        } catch (Exception $th) {
            $this->alert('error', $th->getMessage());
        }
    }
}
