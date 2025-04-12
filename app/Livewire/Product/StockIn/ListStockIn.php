<?php

namespace App\Livewire\Product\StockIn;

use App\Enums\StockActivity;
use App\Enums\StockStatus;
use App\Exports\StockInExport;
use App\Models\ProductStock;
use App\Models\StockIn;
use Carbon\Carbon;
use Exception;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ListStockIn extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    public $isOpen = false, $isExport = false;
    public $stockIn;
    public $query = '', $perPage = 10, $sortBy = 'created_at', $sortDirection = 'desc';
    public $total_price;

    public $stockType;

    #[Rule('required')]
    public $start_date, $end_date;

    protected $listeners = [
        'delete'
    ];

    #[Title('Stock In')]

    public function closeModal()
    {
        $this->reset();
        $this->isOpen = false;
        $this->stockIn = null;
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
        return view('livewire.product.stock-in.list-stock-in', [
            'stockIns' => StockIn::select('stock_ins.*')
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function show($stock_in_id) {
        $this->isOpen = true;
        $this->stockIn = stockIn::find($stock_in_id);
    }

    public function deleteAlert($stockIn)
    {
        $this->stockIn = StockIn::find($stockIn);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete this Stock In Data ?',
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
            foreach ($this->stockIn->stockInProducts as $stockInProduct) {
                $productStock = ProductStock::where('id', $stockInProduct->product_stock_id)->first();
                $productStock->update([
                    $this->stockIn->stock_type->value => $productStock[$this->stockIn->stock_type->value] - $stockInProduct->stock,
                    'all_stock' => $productStock->all_stock - $stockInProduct->stock
                ]);
                setStockHistory(
                    $productStock->id,
                    StockActivity::STOCK_IN,
                    StockStatus::REMOVE,
                    $this->stockIn->stock_type->value,
                    NULL,
                    $stockInProduct->stock,
                    NULL,
                    $productStock->all_stock,
                    $productStock->home_stock,
                    $productStock->store_stock,
                    $productStock->pre_order_stock,
                );
                $stockInProduct->delete();
            }
            $this->stockIn->delete();
            $this->alert('success', 'Stock In Data Succesfully Deleted');
        } catch (Exception $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function openModalExport()
    {
        $this->isExport = true;
        $this->isOpen = true;
    }

    public function exportExcel()
    {
        $this->validate();
        $name = "Stock In ". ucwords(str_replace('_', ' ', $this->stockType)) .  " Tanggal " . Carbon::parse($this->start_date)->translatedFormat('d F Y') ." - ". Carbon::parse($this->end_date)->translatedFormat('d F Y') .".xlsx";
        if($this->stockType != null) {
            return Excel::download(new StockInExport($this->start_date, $this->end_date, $this->stockType), $name);
        } else {
            return Excel::download(new StockInExport($this->start_date, $this->end_date), $name);
        }
    }
}
