<?php

namespace App\Livewire\Sale;

use App\Enums\DiscountType;
use App\Enums\KeepStatus;
use App\Models\Group;
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
    public $query = '', $perPage = 10, $sortBy = 'no_sale', $sortDirection = 'desc', $groupIds, $groupId;

    public $total_price, $sub_total_after_discount;
    public $showColumns = [
        'group' => true,
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

    public function mount()
    {
        $this->groupIds = Group::get();
    }
    public function render()
    {
        return view('livewire.sale.list-sale', [
            'sales' => Sale::select('sales.*')
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->where('no_sale', 'like', '%' . $this->query . '%')
                ->where('customers.group_id', 'like', '%' . $this->groupId . '%')
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

    public function show($sale_id) {
        $this->isOpen = true;
        $this->sale = Sale::find($sale_id);
        $this->getTotalPrice();
    }

    public function getTotalPrice()
    {
        $this->total_price = $this->sale->sub_total;
        if(strtolower($this->sale->discount_type) === strtolower(DiscountType::PERSEN)) {
            $this->sub_total_after_discount = $this->sale->sub_total - round($this->sale->sub_total* (int) $this->sale->discount/100);
            $this->total_price = $this->sub_total_after_discount;
        } elseif(strtolower($this->sale->discount_type) === strtolower(DiscountType::RUPIAH)) {
            $this->sub_total_after_discount = $this->sale->sub_total - $this->sale->discount;
            $this->total_price = $this->sub_total_after_discount;
        } else {
            $this->sub_total_after_discount = $this->total_price;
        }
        if($this->sale->tax) {
            $this->total_price = $this->sub_total_after_discount + round($this->sub_total_after_discount* (int) $this->sale->tax/100);
        }
        if($this->sale->ship) {
            $this->total_price = $this->total_price + $this->sale->ship;
        }
    }

    public function closeModal()
    {
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
                $saleItem->productStock->update([
                    'all_stock' => $saleItem->productStock->all_stock + $saleItem->total_items,
                    $stockStype => $saleItem->productStock->$stockStype + $saleItem->total_items,
                ]);
            }
            if($this->sale->Keep()->exists()){
                $this->sale->keep->update([
                    'status' => KeepStatus::CANCELED
                ]);
            }

            $this->sale->delete();
            $this->alert('success', 'Sale Succesfully Deleted');
        } catch (Exception $th) {
            $this->alert('error', $th->getMessage());
        }
    }
}
