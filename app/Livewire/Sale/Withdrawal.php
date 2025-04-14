<?php

namespace App\Livewire\Sale;

use App\Models\Sale;
use App\Models\SaleWithdrawal;
use Exception;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Withdrawal extends Component
{
    use WithPagination, LivewireAlert;
    public $withdrawal, $sales, $withdrawal_id;

    #[Title('Withdrawal')]

    public $isOpen = false;
    public $query = '', $perPage = 10, $sortBy = 'no_sale', $sortDirection = 'desc';

    #[Validate('required')]
    public $date, $withdrawal_amount, $marketplace_price, $sale_id;

    public $showColumns = [
        'sale_date' => true,
        'total_items' => true,
        'total_sale' => true,
        'no_resi' => true,
        'order_id_marketplace' => true,
        'marketplace_id' => true,
        'withdrawal_date' => true,
        'withdrawal_amount' => true,
        'marketplace_price' => true,
        'created_at' => false,
        'updated_at' => false,
    ];

    protected $listeners = [
        'delete'
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

    public function searchSale($query)
    {
        $this->sales = Sale::whereHas('customer', function($query){
            $query->where('group_id', 2);
        })->whereDoesntHave('saleWithdrawal')
        ->pluck('no_sale','id')->toArray();

        if ($query) {
            $this->sales = Sale::whereHas('customer', function($q){
                $q->where('group_id', 2);
            })->where('no_sale', 'like', '%'.$query.'%')
            ->whereDoesntHave('saleWithdrawal')
            ->pluck('no_sale','id')->toArray();
        }
    }

    public function render()
    {
        return view('livewire.sale.withdrawal', [
            'withdrawals' => SaleWithdrawal::join('sales', 'sales.id', '=', 'sale_withdrawals.sale_id')
                ->leftJoin('sale_shippings', 'sale_shippings.sale_id', '=', 'sale_withdrawals.sale_id')
                ->leftJoin('marketplaces', 'marketplaces.id', '=', 'sale_shippings.marketplace_id')
                ->select(
                    'sale_withdrawals.id',
                    'sale_withdrawals.marketplace_price',
                    'sale_withdrawals.withdrawal_amount',
                    'sale_withdrawals.date',
                    'sales.customer_id',
                    'sales.no_sale',
                    'sales.total_items',
                    'sales.total_price',
                    'sales.created_at as sale_date',
                    'sale_shippings.no_resi',
                    'sale_shippings.customer_name as customer_name',
                    'sale_shippings.order_id_marketplace',
                    'marketplaces.name as marketplace_name'
                )
                ->where(function($query) {
                    $query->where('sale_shippings.no_resi', 'like', '%' . $this->query . '%')
                        ->orWhere('sales.no_sale', 'like', '%' . $this->query . '%');
                })
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage),
        ]);

    }

    public function openModal()
    {
        $this->resetForm();
        if (!$this->withdrawal) {
            $this->sales = Sale::whereHas('customer', function($query){
                $query->where('group_id', 2);
            })->whereDoesntHave('saleWithdrawal')
            ->pluck('no_sale','id')->toArray();
        }
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function save()
    {
        try {
            $this->validate();
            $shipping = SaleWithdrawal::firstOrCreate(['sale_id' => $this->sale_id],[
                'date' => $this->date,
                'withdrawal_amount' => $this->withdrawal_amount,
                'marketplace_price' => $this->marketplace_price,
            ]);

            $this->alert('success', 'Withdrawal Successfully Created');
            $this->closeModal();
        } catch (Exception $th) {
            $this->alert('error', 'Can\'t Create Product', [
                'text' => $th->getMessage()
            ]);
        }
    }

    public function edit($withdrawal_id)
    {
        $this->withdrawal = SaleWithdrawal::where('id', $withdrawal_id)->first();
        if($this->withdrawal) {
            $this->date = $this->withdrawal->date;
            $this->withdrawal_amount = $this->withdrawal->withdrawal_amount;
            $this->marketplace_price = $this->withdrawal->marketplace_price;
            $this->isOpen = true;
        }
    }

    public function update()
    {
        $this->validate();
        try {
            $this->withdrawal->update([
                'date' => $this->date,
                'withdrawal_amount'=> $this->withdrawal_amount,
                'marketplace_price'=> $this->marketplace_price
            ]);

            $this->alert('success', 'Withdrawal Succesfully Updated');
            $this->isOpen = false;
            $this->resetForm();
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function deleteAlert($withdrawal_id)
    {
        $this->withdrawal_id = $withdrawal_id;
        $withdrawal = SaleWithdrawal::where('id', $this->withdrawal_id)->first();
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete withdrawal ' . $withdrawal->sale->no_sale .' ?',
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
        $withdrawal = SaleWithdrawal::where('id', $this->withdrawal_id)->first();
        try {
            $withdrawal->delete();
            $this->withdrawal_id = null;
            $this->withdrawal = null;
            $this->alert('success','Withdrawal Successfully Deleted');
        } catch (\Throwable $th) {
            $this->alert('error',$th->getMessage());
        }
    }

    public function resetForm()
    {
        $this->withdrawal = null;
        $this->withdrawal_id = null;
        $this->date = null;
        $this->withdrawal_amount = null;
        $this->sale_id = null;
        $this->marketplace_price = null;
    }
}
