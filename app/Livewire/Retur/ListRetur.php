<?php

namespace App\Livewire\Retur;

use App\Enums\ReturStatus;
use App\Enums\StockActivity;
use App\Enums\StockStatus;
use App\Exports\ReturExport;
use App\Exports\ReturProductExport;
use App\Models\ProductStock;
use App\Models\Retur;
use App\Models\Setting;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ListRetur extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    public $isOpen = false, $modal;

    #[Validate('required')]
    public $start_date, $end_date, $exportType = 'product';
    public $retur;
    public $query = '', $perPage = 10, $sortBy = 'no_retur', $sortDirection = 'desc', $status, $returStatus;
    public $total_price;
    public $showColumns = [
        'status' => true,
        'reason' => true,
        'group' => true,
        'total_items' => true,
        'total_price' => true,
        'created_at' => false,
        'updated_at' => false,
    ];

    protected $listeners = [
        'delete'
    ];

    #[Title('Retur')]

    public function closeModal()
    {
        $this->isOpen = false;
        $this->retur = null;
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
        return view('livewire.retur.list-retur', [
            'returs' => Retur::select('returs.*')
                ->join('sales', 'returs.sale_id', '=', 'sales.id')
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->where('status', 'like', '%' . $this->status . '%')
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function show($retur_id) {
        $this->isOpen = true;
        $this->retur = Retur::find($retur_id);
        $this->total_price = array_sum(array_column($this->retur->returItems->toArray(), 'total_price'));
    }

    public function deleteAlert($retur)
    {
        $this->retur = Retur::find($retur);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete this retur ?',
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
            $stockType = $this->retur->sale->customer->group_id == 1 ? 'store_stock' : 'home_stock';
            if (strtolower($this->retur->status) == strtolower(ReturStatus::BACK_TO_STOCK)) {
                foreach ($this->retur->returItems as $returItem) {
                    $returItem->productStock->update([
                        $stockType => $returItem->productStock->$stockType - $returItem->total_items,
                        'all_stock' => $returItem->productStock->all_stock - $returItem->total_items,
                    ]);
                    $productStock = ProductStock::where('id', $returItem->product_stock_id)->first();
                    setStockHistory(
                        $returItem->productStock->id,
                        StockActivity::RETUR,
                        StockStatus::REMOVE,
                        $stockType,
                        NULL,
                        $returItem->total_items,
                        $this->retur->no_retur,
                        $productStock->all_stock,
                        $productStock->home_stock,
                        $productStock->store_stock,
                        $productStock->pre_order_stock,
                    );
                }
            }
            $this->retur->delete();
            $this->alert('success', 'Retur Data Succesfully Deleted');
        } catch (Exception $th) {
            $this->alert('error', $th);
        }
    }

    public function changeStatus($retur_id)
    {
        $this->retur = Retur::where('id', $retur_id)->first();
        $this->returStatus = strtolower($this->retur->status);
        $this->modal = 'status';
        $this->isOpen = true;
    }

    public function updateStatus()
    {
        try {
            $stockType = $this->retur->sale->customer->group_id == 1 ? 'store_stock' : 'home_stock';
            if(strtolower($this->returStatus) == strtolower(ReturStatus::BACK_TO_STOCK) && strtolower($this->returStatus) != strtolower($this->retur->status)) {
                foreach ($this->retur->returItems as $returItem) {
                    $productStock = ProductStock::where('id', $returItem->product_stock_id)->first();
                    $productStock->update([
                        $stockType => $productStock->$stockType + $returItem->total_items,
                        'all_stock' => $productStock->all_stock + $returItem->total_items
                    ]);
                }
            } elseif(strtolower($this->retur->status) == strtolower(ReturStatus::BACK_TO_STOCK) && strtolower($this->returStatus) != strtolower(ReturStatus::BACK_TO_STOCK)) {
                foreach ($this->retur->returItems as $returItem) {
                    $productStock = ProductStock::where('id', $returItem->product_stock_id)->first();
                    $productStock->update([
                        $stockType => $productStock->$stockType - $returItem->total_items,
                        'all_stock' => $productStock->all_stock - $returItem->total_items
                    ]);
                }
            }
            $this->retur->update([
                'status' => $this->returStatus
            ]);
            $this->retur = null;
            $this->isOpen = false;
            $this->returStatus = null;
            $this->modal = null;
            $this->alert('success', 'Status Successfully updated');
        } catch (\Throwable $th) {
            $this->alert('error',$th->getMessage());
        }
    }

    public function printPayment($retur_id)
    {
        $retur = Retur::where('id', $retur_id)->first();
        $setting = Setting::first();
        Session::put('retur', $retur);
        Session::put('setting', $setting);
        $this->dispatch('print-retur-payment',route('print-retur-payment', ['retur' => $retur->id]));
    }

    public function openModalExport()
    {
        $this->modal = 'export';
        $this->isOpen = true;
    }

    public function exportExcel()
    {
        if($this->exportType == 'product') {
            $this->validate();
            $name = "Data Retur Produk Tanggal " . Carbon::parse($this->start_date)->translatedFormat('d F Y') ." - ". Carbon::parse($this->end_date)->translatedFormat('d F Y') .".xlsx";
            return Excel::download(new ReturProductExport($this->start_date, $this->end_date), $name);
        } elseif($this->exportType == 'retur') {
            $this->validate();
            $name = "Data Penjualan Tanggal " . Carbon::parse($this->start_date)->translatedFormat('d F Y') ." - ". Carbon::parse($this->end_date)->translatedFormat('d F Y') .".xlsx";
            return Excel::download(new ReturExport($this->start_date, $this->end_date), $name);
        }
        $this->start_date = null;
        $this->end_date = null;
        $this->exportType = 'product';
        $this->modal = null;
    }
}
