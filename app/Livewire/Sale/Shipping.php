<?php

namespace App\Livewire\Sale;

use App\Models\Bank;
use App\Models\Marketplace;
use App\Models\Sale;
use App\Models\SaleShipping;
use Exception;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Shipping extends Component
{
    use WithPagination, LivewireAlert;
    public $shipping, $sales, $shipping_id, $banks;
    public $isOpen = false, $modal;
    public $query = '', $perPage = 10, $sortBy = 'no_sale', $sortDirection = 'desc';

    public $marketplace;

    #[Validate('required')]
    public $date, $cost, $no_resi, $order_id_marketplace, $marketplace_id, $status, $customer_name, $city, $address, $sale_id;

    public $bank_id, $transfer_amount;

    #[Validate('required|regex:/^8\d+$/')]
    public $phone;

    #[Title('Shipping')]

    protected $listeners = [
        'delete'
    ];

    public $showColumns = [
        'sale_date' => true,
        'total_items' => true,
        'total_price' => true,
        'ship_date' => true,
        'ship_status' => true,
        'no_resi' => true,
        'order_id_marketplace' => true,
        'marketplace_id' => true,
        'created_at' => false,
        'updated_at' => false,
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

    public function mount()
    {
        $this->banks = Bank::all()->pluck('short_name', 'id')->toArray();
        $this->marketplace = Marketplace::all()->pluck('name','id')->toArray();
        $this->sales = Sale::whereHas('customer', function($query){
            $query->where('group_id', 2);
        })->doesntHave('saleShipping')->pluck('no_sale','id')->toArray();
    }

    public function updatedSaleId()
    {
        $sale = Sale::where('id', $this->sale_id)->first();
        $this->cost = $sale->ship;
    }

    public function updatedMarketplaceId()
    {
        if($this->shipping) {
            if($this->marketplace_id === 4 || $this->marketplace_id === 5) {
                $this->bank_id = $this->shipping->bank_id;
                $this->transfer_amount = $this->shipping->transfer_amount;
            }
        }
    }

    public function searchSale($query)
    {
        $this->sales = Sale::whereHas('customer', function($query){
            $query->where('group_id', 2);
        })->doesntHave('saleShipping')->pluck('no_sale','id')->toArray();
        if ($query) {
            $this->sales = Sale::whereHas('customer', function($q){
                $q->where('group_id', 2);
            })
            ->where('no_sale', 'like', '%'.$query.'%')
            ->doesntHave('saleShipping')
            ->pluck('no_sale','id')->toArray();
        }
    }


    public function render()
    {
        return view('livewire.sale.shipping', [
            'saleShippings' => SaleShipping::join('sales', 'sale_shippings.sale_id', '=', 'sales.id')
                ->select('sale_shippings.*', 'sales.customer_id', 'sales.no_sale', 'total_price')
                ->where('no_resi', 'like', '%' . $this->query . '%')
                ->orWhere('order_id_marketplace', 'like', '%' . $this->query . '%')
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage),
        ]);
    }

    public function openModal()
    {
        $this->resetForm();
        $this->modal = 'shipping';
        $this->marketplace = Marketplace::all()->pluck('name','id')->toArray();
        if (!$this->shipping) {
            $this->sales = Sale::whereHas('customer', function($query){
                $query->where('group_id', 2);
            })->doesntHave('saleShipping')->pluck('no_sale','id')->toArray();
        }
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->modal = 'shipping';
        $this->isOpen = false;
    }

    public function save()
    {
        try {
            $this->validate();
            $shipping = SaleShipping::firstOrCreate(['sale_id' => $this->sale_id],[
                'status' => $this->status,
                'date' => $this->date,
                'cost' => $this->cost,
                'no_resi' => $this->no_resi,
                'marketplace_id' => $this->marketplace_id,
                'order_id_marketplace' => $this->order_id_marketplace,
                'customer_name' => $this->customer_name,
                'address' => $this->address,
                'city' => $this->city,
                'phone' => $this->phone,
                'bank_id' => $this->bank_id,
                'transfer_amount' => $this->transfer_amount,
            ]);
            $shipping->sale->update([
                'ship' => $shipping->cost
            ]);

            $this->alert('success', 'Shipping Successfully Created');
            $this->closeModal();
        } catch (Exception $th) {
            $this->alert('error', 'Can\'t Create Product', [
                'text' => $th->getMessage()
            ]);
        }
    }

    public function edit($shipping_id)
    {
        $this->marketplace = Marketplace::all()->pluck('name','id')->toArray();
        $this->shipping = SaleShipping::where('id', $shipping_id)->first();
        if($this->shipping) {
            $this->date = $this->shipping->date;
            $this->cost = $this->shipping->cost;
            $this->no_resi = $this->shipping->no_resi;
            $this->order_id_marketplace = $this->shipping->order_id_marketplace;
            $this->marketplace_id = $this->shipping->marketplace_id;
            $this->status = $this->shipping->status;
            $this->customer_name = $this->shipping->customer_name;
            $this->city = $this->shipping->city;
            $this->address = $this->shipping->address;
            $this->sale_id = $this->shipping->sale_id;
            $this->phone = $this->shipping->phone;
            $this->bank_id = $this->shipping->bank_id;
            $this->transfer_amount = $this->shipping->transfer_amount;
            $this->isOpen = true;
            $this->modal = 'shipping';
        }
    }

    public function update()
    {
        $this->validate();
        try {
            $this->shipping->update([
                'date' => $this->date,
                'cost' => $this->cost,
                'no_resi' => $this->no_resi,
                'order_id_marketplace' => $this->order_id_marketplace,
                'marketplace_id' => $this->marketplace_id,
                'status' => $this->status,
                'customer_name' => $this->customer_name,
                'city' => $this->city,
                'address' => $this->address,
                'sale_id' => $this->sale_id,
                'phone' => $this->phone,
                'bank_id' => $this->bank_id,
                'transfer_amount' => $this->transfer_amount,
            ]);
            $this->shipping->sale->update([
                'ship' => $this->cost
            ]);
            $this->shipping = null;
            $this->alert('success', 'Shipping Succesfully Updated');
            $this->isOpen = false;
            $this->resetForm();
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function changeStatus($shipping_id)
    {
        $this->shipping = SaleShipping::where('id', $shipping_id)->first();
        $this->status = $this->shipping->status;
        $this->modal = 'status';
        $this->isOpen = true;
    }

    public function updateStatus()
    {
        try {
            if(!$this->shipping->sale->saleWithdrawal) {
                $this->shipping->update([
                    'status' => $this->status
                ]);
                $this->shipping = null;
                $this->isOpen = false;
                $this->alert('success','Status Successfully updated');
            } else {
                $this->alert('warning','Withdrawal is created, Can\'t change shipping status');
            }
        } catch (\Throwable $th) {
            $this->alert('error',$th->getMessage());
        }
    }

    public function deleteAlert($shipping_id)
    {
        $this->shipping_id = $shipping_id;
        $shipping = SaleShipping::where('id', $this->shipping_id)->first();
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete shipping ' . $shipping->sale->no_sale .' ?',
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
        $shipping = SaleShipping::where('id', $this->shipping_id)->first();
        try {
            $shipping->delete();
            $this->shipping_id = null;
            $this->alert('success','Shipping Successfully Deleted');
        } catch (\Throwable $th) {
            $this->alert('error',$th->getMessage());
        }
    }

    public function resetForm()
    {
        $this->shipping = null;
        $this->shipping_id = null;
        $this->date = null;
        $this->cost = null;
        $this->no_resi = null;
        $this->order_id_marketplace = null;
        $this->marketplace_id = null;
        $this->status = null;
        $this->customer_name = null;
        $this->city = null;
        $this->address = null;
        $this->sale_id = null;
    }
}
