<?php

namespace App\Livewire\Retur;

use App\Enums\ReturReason;
use App\Enums\ReturStatus;
use App\Models\ProductStock;
use App\Models\Retur;
use App\Models\ReturItem;
use App\Models\Sale;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

class CreateRetur extends Component
{
    use LivewireAlert;

    public string $subtitle = 'Retur';
    public string $subRoute = 'retur';

    #[Rule(['required'])]
    public $sale_id;
    public $sales, $sale, $isEdit, $sales_ids;

    public $customer_name, $group_name;
    public $no_retur;

    #[Rule(['required'])]
    public $status = ReturStatus::BACK_TO_STOCK, $reason = ReturReason::SWAP_ITEM;
    public $item_status;
    public $retur;

    public $desc;
    public $saleItems = [], $returItems = [], $total_items, $total_price, $returItem;

    public $isOpen = false;

    #[Title('Create Retur')]
    #[Layout('layouts.app')]

    protected $listeners = [
        'deleteReturnItem'
    ];

    public function mount($retur = null)
    {
        View::share('subtitle', $this->subtitle);
        View::share('subRoute', $this->subRoute);
        $this->sales_ids = Retur::get()->pluck('sale_id')->toArray();
        $this->sales = Sale::whereNotIn('id', $this->sales_ids)->pluck('no_sale', 'id')->toArray();
        if($retur) {
            $this->retur = Retur::where('id', $retur)->first();
            if($this->retur) {
                $this->no_retur = $this->retur->no_retur;
                $this->edit();
                $this->getTotalPrice();
            } else {
                return redirect()->route('retur')->with('error', 'Keep Order Not Found');
            }
        } else {
            $setting = Setting::first();
            $this->no_retur = $setting->retur_code . str_pad($setting->retur_increment + 1, 4, '0', STR_PAD_LEFT);
        }
    }

    public function render()
    {
        return view('livewire.retur.create-retur')->with('subtitle', $this->subtitle);
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function searchSales($query)
    {
        $this->sales = Sale::whereNotIn('id', $this->sales_ids)->pluck('no_sale', 'id')->toArray();
        if ($query) {
            $this->sales = Sale::whereNotIn('id', $this->sales_ids)->where('no_sale', 'like', '%'.$query.'%')
                ->pluck('no_sale', 'id')
                ->toArray();
            }
    }

    public function updatedSaleId()
    {
        $this->saleItems = [];
        $this->sale = Sale::where('id', $this->sale_id)->first();
        $this->customer_name = $this->sale->customer->name;
        $this->group_name = $this->sale->customer->group->name;
        foreach ($this->sale->saleItems as $saleItem) {
            $this->saleItems[$saleItem->product_stock_id] = [
                'id' => $saleItem->product_stock_id,
                'color' => $saleItem->productStock->color->name,
                'size' => $saleItem->productStock->size->name,
                'product' => $saleItem->productStock->product->name,
                'total_items' => $saleItem->total_items,
                'price' => $saleItem->price,
                'total_price' => $saleItem->total_price,
            ];
        }
        $this->getTotalPrice();
    }

    public function getTotalPrice()
    {
        $this->total_items = array_sum(array_column($this->returItems, 'total_items'));
        $this->total_price = array_sum(array_column($this->returItems, 'total_price'));
    }

    public function returProduct($product_stock_id)
    {
        $this->returItems[$product_stock_id] = [
            'id' => $product_stock_id,
            'color' => $this->saleItems[$product_stock_id]['color'],
            'size' => $this->saleItems[$product_stock_id]['size'],
            'product' => $this->saleItems[$product_stock_id]['product'],
            'total_items' => 1,
            'price' => $this->saleItems[$product_stock_id]['price'],
            'total_price' => $this->saleItems[$product_stock_id]['price'],
            'item_status' => 'vermak',
        ];
        $this->getTotalPrice();
    }

    public function addReturnItem($productStockId)
    {
        if($this->saleItems[$productStockId]['total_items'] > $this->returItems[$productStockId]['total_items']) {
            $this->returItems[$productStockId]['total_items']++;
            $this->returItems[$productStockId]['total_price'] = $this->returItems[$productStockId]['total_items'] * $this->returItems[$productStockId]['price'];
            $this->getTotalPrice();
        } else {
            $this->alert('warning', "Out Of Item");
        }
    }

    public function removeReturnItem($productStockId)
    {
        if($this->returItems[$productStockId]['total_items'] > 1) {
            $this->returItems[$productStockId]['total_items']--;
            $this->returItems[$productStockId]['total_price'] = $this->returItems[$productStockId]['total_items'] * $this->returItems[$productStockId]['price'];
            $this->getTotalPrice();
        } else {
            $this->returItem = $this->returItems[$productStockId]['id'];
            $this->alert('question', 'Delete', [
                'toast' => false,
                'text' => 'Are you sure to remove ' . $this->returItems[$productStockId]['product'] .' ?',
                'position' => 'center',
                'showConfirmButton' => true,
                'confirmButtonText' => 'Yes',
                'showCancelButton' => true,
                'cancelButtonText' => 'cancel',
                'icon' => 'warning',
                'onConfirmed' => 'deleteReturnItem',
                'timer' => null,
                'confirmButtonColor' => '#3085d6',
                'cancelButtonColor' => '#d33'
            ]);
        }
    }

    public function deleteReturnItem()
    {
        unset($this->returItems[$this->returItem]);
        $this->returItem = null;
        $this->getTotalPrice();
    }

    public function changeItemStatus($productStockId)
    {
        $this->item_status = $this->returItems[$productStockId]['item_status'];
        $this->returItem = $this->returItems[$productStockId]['id'];
        $this->isOpen = true;
    }

    public function saveReturStatus()
    {
        $this->returItems[$this->returItem]['item_status'] = strtolower($this->item_status);
        $this->item_status = null;
        $this->isOpen = false;
        $this->returItem = null;
    }

    public function createReturProduct($retur_id)
    {
        $retur = Retur::where('id', $retur_id)->first();
        $stockType = $retur->sale->customer->group_id == 1 ? 'store_stock' : 'home_stock';
        foreach ($this->returItems as $returItem) {
            $productStock = ProductStock::where('id', $returItem['id'])->first();
            if($retur->status == strtolower(ReturStatus::BACK_TO_STOCK)) {
                $productStock->update([
                    $stockType => $productStock->$stockType + $returItem['total_items'],
                    'all_stock' => $productStock->all_stock + $returItem['total_items']
                ]);
            }
            ReturItem::create([
                'retur_id' => $retur_id,
                'product_stock_id' => $returItem['id'],
                'total_items' => $returItem['total_items'],
                'price' => $returItem['price'],
                'total_price' => $returItem['total_price'],
                'status' => $returItem['item_status'],
            ]);
        }
    }

    public function save()
    {
        $this->validate();
        $setting = Setting::first();
        try {
            if($this->returItems) {
                $retur = Retur::create([
                    'status' => strtolower($this->status),
                    'reason' => strtolower($this->reason),
                    'sale_id' => $this->sale_id,
                    'user_id' => Auth::user()->id,
                    'no_retur' => $this->no_retur,
                    'total_price' => $this->total_price,
                    'total_items' => $this->total_items,
                    'desc' => $this->desc,
                ]);

                $setting->update([
                    'retur_increment' => $setting->retur_increment + 1
                ]);

                $this->createReturProduct($retur->id);
                $this->reset();
                $this->alert('success', 'Retur Succesfully Created');
                return redirect()->route('retur');
            } else {
                $this->alert('warning','No Product to Return');
            }
        } catch (\Throwable $th) {
            $this->alert('warning', $th->getMessage());
        }
    }

    public function edit()
    {
        $this->isEdit = true;
        $this->group_name = $this->retur->sale->customer->group->name;
        $this->customer_name = $this->retur->sale->customer->name;
        $this->sale_id = $this->retur->sale_id;
        $this->status = strtolower($this->retur->status);
        $this->reason = strtolower($this->retur->reason);
        foreach ($this->retur->returItems as $returItem) {
            $this->returItems[$returItem->product_stock_id] = [
                'id' => $returItem->product_stock_id,
                'color' => $returItem->productStock->color->name,
                'size' => $returItem->productStock->size->name,
                'product' => $returItem->productStock->product->name,
                'total_items' => $returItem->total_items,
                'price' => $returItem->price,
                'total_price' => $returItem->total_price,
                'item_status' => strtolower($returItem->status)
            ];
        }

        foreach ($this->retur->sale->saleItems as $saleItem) {
            $this->saleItems[$saleItem->product_stock_id] = [
                'id' => $saleItem->product_stock_id,
                'color' => $saleItem->productStock->color->name,
                'size' => $saleItem->productStock->size->name,
                'product' => $saleItem->productStock->product->name,
                'total_items' => $saleItem->total_items,
                'price' => $saleItem->price,
                'total_price' => $saleItem->total_price,
            ];
        }
    }

    public function update()
    {
        $this->validate();
        $setting = Setting::first();
        try {
            if($this->returItems) {
                $this->retur->update([
                    'status' => strtolower($this->status),
                    'reason' => strtolower($this->reason),
                    'sale_id' => $this->sale_id,
                    'user_id' => Auth::user()->id,
                    'no_retur' => $this->no_retur,
                    'total_price' => $this->total_price,
                    'total_items' => $this->total_items,
                    'desc' => $this->desc,
                ]);

                foreach ($this->retur->returItems as $returItem) {
                    $stockType = $this->retur->sale->customer->group_id == 1 ? 'store_stock' : 'home_stock';
                    $productStock = ProductStock::where('id', $returItem->product_stock_id)->first();
                    $productStock->update([
                        $stockType => $productStock->$stockType - $returItem->total_items,
                        'all_stock' => $productStock->all_stock - $returItem->total_items
                    ]);
                    $returItem->delete();
                }

                $this->createReturProduct($this->retur->id);
                $this->reset();
                $this->alert('success', 'Retur Succesfully Updated');
                return redirect()->route('retur');
            } else {
                $this->alert('warning','No Product to Return');
            }
        } catch (\Throwable $th) {
            $this->alert('warning', $th->getMessage());
        }
    }
}
