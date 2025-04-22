<?php

namespace App\Livewire\Sale;

use App\Enums\DiscountType;
use App\Models\Sale;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class OnlineSales extends Component
{
    use WithPagination;
    public $sale;
    public $isOpen = false;
    public $query = '', $perPage = 10, $sortBy = 'no_sale', $sortDirection = 'desc';

    public $total_price, $sub_total_after_discount;

    #[Title('Online Sales')]

    public $openRows = [];

    public $showColumns = [
        'marketplace' => true,
        'order_id_marketplace' => true,
        'total_items' => true,
        'total_price' => true,
        'ship_status' => true,
        'ship_cost' => true,
        'withdrawal_status' => true,
        'withdrawal_amount' => true,
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

    public function toggleRow($productId)
    {
        if (in_array($productId, $this->openRows)) {
            $this->openRows = array_diff($this->openRows, [$productId]);
        } else {
            $this->openRows[] = $productId;
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.sale.online-sales', [
                'onlineSales' => Sale::join('customers', 'sales.customer_id', '=', 'customers.id')
                    ->leftJoin('sale_shippings', 'sales.id', '=', 'sale_shippings.sale_id')
                    ->leftJoin('marketplaces', 'sales.marketplace_id','=','marketplaces.id')
                    ->leftJoin('sale_withdrawals', 'sales.id', '=', 'sale_withdrawals.sale_id')
                    ->with('saleItems')
                    ->select(
                        'sales.id as id',
                        'sales.no_sale',
                        'customers.name as customer_name',
                        'sales.total_items',
                        'sales.total_price',
                        'sales.total_price',
                        'sales.created_at',
                        'sales.updated_at',
                        'sale_shippings.status as ship_status',
                        'sale_shippings.cost as ship_cost',
                        'sale_withdrawals.withdrawal_amount as withdrawal_amount',
                        'marketplaces.name as marketplace_name',
                        'sales.order_id_marketplace as order_id',
                    )
                    ->whereHas('customer', function($query){
                        return $query->where('group_id', 2);
                    })
                    ->where('pre_order_id', null)
                    ->where('no_sale', 'like', '%' . $this->query . '%')
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage)
            ]);
    }

    public function show($sale_id)
    {
        $this->sale = Sale::find($sale_id);
        $this->isOpen = true;
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
}
