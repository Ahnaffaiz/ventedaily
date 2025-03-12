<?php

namespace App\Livewire\Sale;

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
    public $shipping, $sales;
    public $isOpen = false;
    public $query = '', $perPage = 10, $sortBy = 'no_sale', $sortDirection = 'desc';

    public $marketplace;

    #[Validate('required')]
    public $date, $cost, $no_resi, $order_id_marketplace, $marketplace_id, $status, $customer_name, $city, $address, $sale_id;

    #[Validate('required|regex:/^8\d+$/')]
    public $phone;

    #[Title('Shipping')]

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
                ->where('no_resi', 'like', '%' . $this->query . '%')
                ->orWhere('order_id_marketplace', 'like', '%' . $this->query . '%')
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage),
        ]);
    }

    public function openModal()
    {
        $this->resetForm();
        $this->marketplace = Marketplace::all()->pluck('name','id')->toArray();
        $this->sales = Sale::whereHas('customer', function($query){
            $query->where('group_id', 2);
        })->doesntHave('saleShipping')->pluck('no_sale','id')->toArray();
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

    public function resetForm()
    {
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
