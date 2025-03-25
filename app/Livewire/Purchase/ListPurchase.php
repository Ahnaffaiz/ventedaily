<?php

namespace App\Livewire\Purchase;

use App\Enums\DiscountType;
use App\Exports\PurchaseExport;
use App\Exports\PurchaseProductExport;
use App\Models\Purchase;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ListPurchase extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    public $purchase, $sub_total_after_discount;
    public $isOpen = false, $isPayment = false, $isExport = false;
    public $query = '', $perPage = 10, $sortBy = 'created_at', $sortDirection = 'desc';

    #[Rule('required')]
    public $start_date, $end_date, $exportType = 'product';
    public $supplier_id, $suppliers;
    public $showColumns = [
        'supplier_id' => true,
        'term_of_payment_id' => true,
        'sub_total' => true,
        'discount' => false,
        'tax' => true,
        'total_price' => true,
        'outstanding_balance' => true,
        'created_at' => true,
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

    public function getTotalPrice()
    {
        if(strtolower($this->purchase->discount_type) === strtolower(DiscountType::PERSEN) ) {
            $this->sub_total_after_discount = $this->purchase->sub_total - round($this->purchase->sub_total* (int) $this->purchase->discount/100);
        } elseif(strtolower($this->purchase->discount_type) === strtolower(DiscountType::RUPIAH)) {
            $this->sub_total_after_discount = $this->purchase->sub_total - $this->purchase->discount;
        } else {
            $this->sub_total_after_discount = $this->purchase->total_price;
        }
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

    public function show($purchase_id) {
        $this->purchase = Purchase::find($purchase_id);
        $this->getTotalPrice();
        $this->isOpen = true;
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
        DB::beginTransaction();
        try {
            foreach ($this->purchase->purchaseItems as $purchaseItem) {
                $productStock = $purchaseItem->productStock;
                if ($productStock->home_stock < $purchaseItem->total_items) {
                    $this->alert('warning', "Stok tidak mencukupi untuk membatalkan transaksi");
                    DB::rollBack();
                    return;
                }
            }

            foreach ($this->purchase->purchaseItems as $purchaseItem) {
                $productStock = $purchaseItem->productStock;
                $productStock->update([
                    'all_stock' => $productStock->all_stock - $purchaseItem->total_items,
                    'home_stock' => $productStock->home_stock - $purchaseItem->total_items,
                ]);
            }
            $this->purchase->delete();
            $this->alert('success', 'Purchase Succesfully Deleted');

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $this->alert('error', 'Tidak dapat menghapus data');
        }
    }

    public function openModalExport()
    {
        $this->suppliers = Supplier::all()->pluck('name', 'id')->toArray();
        $this->isExport = true;
        $this->isOpen = true;
    }

    public function searchSupplier($query)
    {
        $this->suppliers = Supplier::all()->pluck('name', 'id')->toArray();
        if ($query) {
            $this->suppliers = Supplier::where('name', 'like', '%'.$query.'%')
            ->pluck('name', 'id')
            ->toArray();
        }
    }

    public function exportExcel()
    {
        if($this->exportType == 'product') {
            $this->validate();
            $name = "Data Pembelian Product Tanggal " . Carbon::parse($this->start_date)->translatedFormat('d F Y') ." - ". Carbon::parse($this->end_date)->translatedFormat('d F Y') .".xlsx";
            return Excel::download(new PurchaseProductExport($this->start_date, $this->end_date), $name);
        } elseif($this->exportType == 'purchase') {
            $this->validate();
            $name = "Data Pembelian Tanggal " . Carbon::parse($this->start_date)->translatedFormat('d F Y') ." - ". Carbon::parse($this->end_date)->translatedFormat('d F Y') .".xlsx";
            if($this->supplier_id) {
                return Excel::download(new PurchaseExport($this->start_date, $this->end_date, $this->supplier_id), $name);
            } else {
                return Excel::download(new PurchaseExport($this->start_date, $this->end_date), $name);
            }
        }
        $this->start_date = null;
        $this->end_date = null;
        $this->exportType = 'product';
        $this->isExport = false;
    }


}
