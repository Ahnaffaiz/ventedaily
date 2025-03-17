<?php

namespace App\Livewire\PreOrder;

use App\Enums\PreOrderStatus;
use App\Enums\PreOrderType;
use App\Models\Customer;
use App\Models\Group;
use App\Models\PreOrder;
use App\Models\PreOrderProduct;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

class CreatePreOrder extends Component
{
    use LivewireAlert;

    public string $subtitle = 'PreOrder';
    public string $subRoute = 'pre-order';

    public $customers;
    public $preOrder, $isEdit;
    public $productStockList, $product_id, $productStock, $products;

    #[Rule('required')]
    public $customer_id;

    #[Rule('required')]
    public $pre_order_type;

    #[Rule('required')]
    public $pre_order_time;

    public $desc;
    public $cart = [], $total_items, $total_price;

    public $isOpen = false;

    #[Title('Create Pre Order')]
    #[Layout('layouts.app')]

    protected $listeners = [
        'deleteProductStock'
    ];

    public function mount($preorder = null)
    {
        View::share('subtitle', $this->subtitle);
        View::share('subRoute', $this->subRoute);
        $this->products = Product::all()->pluck('name', 'id')->toArray();
        $this->customers = Customer::get()->pluck('name', 'id')->toArray();
        if($preorder) {
            $this->preOrder = PreOrder::where('id', $preorder)->first();
            if(strtolower($this->preOrder->status) === strtolower(PreOrderStatus::ACTIVE)) {
                $this->edit();
                $this->getTotalPrice();
            } else {
                return redirect()->route('preOrder')->with('error', 'PreOrder Order Not Found');
            }
        } else {
            $PreOrderTimeout = Setting::first()->pre_order_timeout;
            $this->pre_order_type = PreOrderType::REGULAR;
            $this->pre_order_time = Carbon::tomorrow()->setTimeFromTimeString($PreOrderTimeout);
        }
    }

    public function render()
    {
        return view('livewire.pre-order.create-pre-order')->with('subtitle', $this->subtitle);
    }

    public function openModal()
    {
        $this->validate();
        if($this->customer_id == null) {
            $this->alert('warning', 'Customer Type Not Defined');
        } else {
            $this->isOpen = true;
        }
    }

    public function closeModal()
    {
        $this->product_id = null;
        $this->productStockList = null;
        $this->isOpen = false;
    }

    public function searchProduct($query)
    {
        $this->products = Product::all()->pluck('name', 'id')->toArray();
        if ($query) {
            $this->products = collect(Product::all()->pluck('name', 'id')->toArray())
                ->filter(function ($label, $value) use ($query) {
                    return stripos($label, $query) !== false;
                })
                ->toArray();
            }
    }

    public function updatedPreOrderType()
    {
        if (strtolower($this->pre_order_type) === PreOrderType::REGULAR) {
            $setting = Setting::first();
            if($setting?->pre_order_timeout == null) {
                $this->alert('warning', 'PreOrder Timeout Belum diatur');
                $this->pre_order_type = null;
            } else {
                $PreOrderTimeout = Setting::first()->pre_order_timeout;
                $this->pre_order_time = Carbon::tomorrow()->setTimeFromTimeString($PreOrderTimeout);
            }
        } else {
            $this->pre_order_time = null;
        }
    }

    public function updatedProductId()
    {
        $this->productStockList = ProductStock::with('color', 'size')->where('product_id', $this->product_id)->get()->toArray();
        $this->productStockList = collect($this->productStockList)->map(function ($stockItem) {
            $cartItem = collect($this->preOrder?->preOrderProducts->keyBy('product_stock_id')->toArray())->firstWhere('product_stock_id', $stockItem['id']);
            if ($cartItem) {
                $stockItem['pre_order_stock'] += $cartItem['total_items'];
            }
            return $stockItem;
        })->toArray();
    }

    public function getTotalPrice()
    {
        $this->total_items = array_sum(array_column($this->cart, 'quantity'));
        $this->total_price = array_sum(array_column($this->cart, 'total_price'));
    }

    public function addToCart($productStockId)
    {
        $productStock = ProductStock::where('id', $productStockId)->first();
        $this->cart[$productStockId]['selling_price'] = $productStock->selling_price;
        if(!array_key_exists('pre_order_stock', $this->cart[$productStockId])) {
            $this->cart[$productStockId]['pre_order_stock'] = ProductStock::where('id', $productStockId)->first()->pre_order_stock;
        }


        if($this->cart[$productStockId]['quantity'] > $this->cart[$productStockId]['pre_order_stock']) {
            $this->cart[$productStockId]['quantity'] = $this->cart[$productStockId]['pre_order_stock'];
            $this->alert('warning', 'Stock Not Enough');
        } elseif($this->cart[$productStockId]['quantity'] == null) {
            $this->cart[$productStockId]['quantity'] = 0;
            $this->productStock = $this->cart[$productStockId]['id'];
            $this->deleteProductStock();
        } elseif($this->cart[$productStockId]['quantity'] < 1) {
            $this->cart[$productStockId]['quantity'] = 1;
        }

        if($this->cart[$productStockId]['quantity'] >= 1 && $this->cart[$productStockId]['quantity'] <= $this->cart[$productStockId]['pre_order_stock']) {
            $this->cart[$productStockId] = [
                'id' => $productStock->id,
                'color' => $productStock->color->name,
                'size' => $productStock->size->name,
                'product' => $productStock->product->name,
                'quantity' => $this->cart[$productStockId]['quantity'],
                'pre_order_stock' => $this->cart[$productStockId]['pre_order_stock'],
                'purchase_price' => $productStock->purchase_price,
                'selling_price' => $productStock->selling_price,
                'total_price' => $this->cart[$productStockId]['selling_price'] * $this->cart[$productStockId]['quantity']
            ];
            $this->getTotalPrice();
        }
    }

    public function addProductStock($productStockId)
    {
        if (!isset($this->cart[$productStockId])) {
            $preOrderProduct = $this->preOrder?->preOrderProducts()->where('product_stock_id', $productStockId)->first();
            $this->cart[$productStockId]['pre_order_stock'] = ProductStock::where('id', $productStockId)->first()->pre_order_stock + $preOrderProduct?->total_items;
            if ($this->cart[$productStockId]['pre_order_stock'] > 0) {
                $this->cart[$productStockId]['quantity'] = 1;
                $this->addToCart($productStockId);
            } else {
                $this->alert('warning', "Out Of Stock");
            }

        } else {
            if($this->cart[$productStockId]['pre_order_stock'] > $this->cart[$productStockId]['quantity']) {
                $this->cart[$productStockId]['quantity']++;
                $this->addToCart($productStockId);
            } else {
                $this->alert('warning', "Out Of Stock");
            }
        }
    }

    public function removeProductStock($productStockId)
    {
        if (isset($this->cart[$productStockId]) && $this->cart[$productStockId]['quantity'] > 1) {
            $this->cart[$productStockId]['quantity']--;
            $this->addToCart($productStockId);
        } elseif(isset($this->cart[$productStockId]) && $this->cart[$productStockId]['quantity'] == 1) {
            $this->productStock = $this->cart[$productStockId]['id'];
            $this->alert('question', 'Delete', [
                'toast' => false,
                'text' => 'Are you sure to remove ' . $this->cart[$productStockId]['product'] .' ?',
                'position' => 'center',
                'showConfirmButton' => true,
                'confirmButtonText' => 'Yes',
                'showCancelButton' => true,
                'cancelButtonText' => 'cancel',
                'icon' => 'warning',
                'onConfirmed' => 'deleteProductStock',
                'timer' => null,
                'confirmButtonColor' => '#3085d6',
                'cancelButtonColor' => '#d33'
            ]);
        } else {
            $this->alert('warning','Product not added');
        }
    }

    public function deleteProductStock()
    {
        unset($this->cart[$this->productStock]);
        $this->getTotalPrice();
        $this->productStock = null;
        $this->alert('success', 'Product Successfully Deleted');
    }

    public function save()
    {
        $this->validate();
        $setting = Setting::first();
        try {
            $preorder = PreOrder::create([
                'user_id' => Auth::user()->id,
                'status' => strtolower(PreOrderStatus::ACTIVE),
                'no_pre_order' => $setting->pre_order_code . str_pad($setting->pre_order_increment + 1, 4, '0', STR_PAD_LEFT),
                'customer_id' => $this->customer_id,
                'total_price' => $this->total_price,
                'total_items' => $this->total_items,
                'pre_order_time' => $this->pre_order_time,
                'desc' => $this->desc,
            ]);

            $setting->update([
                'pre_order_increment' => $setting->pre_order_increment + 1
            ]);

            foreach ($this->cart as $productStock) {
                $pre_order_stock = 0;
                $stock = ProductStock::where('id', $productStock['id'])->first();
                if ($productStock['pre_order_stock'] < $productStock['quantity']) {
                    $this->alert('warning', 'Stock Pre Order Not Enough');
                } else {
                    $stock->update([
                        'pre_order_stock' => $stock->pre_order_stock - $productStock['quantity'],
                        'all_stock' => $stock->pre_order_stock - $productStock['quantity'] + $stock->store_stock + $stock->home_stock,
                    ]);
                }
                $preOrderProduct = PreOrderProduct::create([
                    'pre_order_id' => $preorder->id,
                    'product_stock_id' => $productStock['id'],
                    'total_items' => $productStock['quantity'],
                    'selling_price' => $productStock['selling_price'],
                    'purchase_price' => $productStock['purchase_price'],
                    'total_price' => $productStock['total_price']
                ]);
            }

            $this->reset();
            $this->alert('success', 'PreOrder Order Succesfully Created');
            $this->mount();
        } catch (\Throwable $th) {
            dd($th);
            $this->alert('warning', $th);
        }
    }

    public function edit()
    {
        $this->isEdit = true;
        $this->customers = Customer::get()->pluck('name', 'id')->toArray();
        $this->customer_id = $this->preOrder->customer_id;
        $this->pre_order_type = $this->preOrder->pre_order_type;
        $this->pre_order_time = $this->preOrder->pre_order_time;

        foreach ($this->preOrder->preOrderProducts as $preOrderProduct) {
            $this->cart[$preOrderProduct->product_stock_id] = [
                'id' => $preOrderProduct->product_stock_id,
                'color' => $preOrderProduct->productStock->color->name,
                'size' => $preOrderProduct->productStock->size->name,
                'product' => $preOrderProduct->productStock->product->name,
                'quantity' => $preOrderProduct->total_items,
                'pre_order_stock' => $preOrderProduct->total_items + $preOrderProduct->productStock->pre_order_stock,
                'purchase_price' => $preOrderProduct->purchase_price,
                'selling_price' => $preOrderProduct->selling_price,
                'total_price' => $preOrderProduct->total_price
            ];
        }
    }

    public function update()
    {
        $this->validate();
        $this->preOrder->update([
            'user_id' => Auth::user()->id,
            'status' => strtolower(PreOrderStatus::ACTIVE),
            'customer_id' => $this->customer_id,
            'total_price' => $this->total_price,
            'total_items' => $this->total_items,
            'pre_order_time' => $this->pre_order_time,
            'desc' => $this->desc,
        ]);

        foreach ($this->preOrder->preOrderProducts as $preOrderProduct) {
            $productStock = ProductStock::where('id', $preOrderProduct->product_stock_id)->first();
            $productStock->update([
                'pre_order_stock' => $productStock['pre_order_stock'] + $preOrderProduct->total_items,
                'all_stock' => $productStock['all_stock'] + $preOrderProduct->total_items
            ]);
            $preOrderProduct->delete();
        }


        foreach ($this->cart as $productStock) {
            $pre_order_stock = 0;
            $stock = ProductStock::where('id', $productStock['id'])->first();
            if ($productStock['pre_order_stock'] < $productStock['quantity']) {
                $this->alert('warning', 'Stock Pre Order Not Enough');
            } else {
                $pre_order_stock = $productStock['quantity'];
                $stock->update([
                    'pre_order_stock' => $stock->pre_order_stock - $productStock['quantity'],
                    'all_stock' => $stock->all_stock - $productStock['quantity'],
                ]);
            }

            $preOrderProduct = PreOrderProduct::create([
                'pre_order_id' => $this->preOrder->id,
                'product_stock_id' => $productStock['id'],
                'total_items' => $productStock['quantity'],
                'selling_price' => $productStock['selling_price'],
                'purchase_price' => $productStock['purchase_price'],
                'total_price' => $productStock['total_price']
            ]);
        }

        $this->alert('success', 'Purchase Order Succesfully Updated');
        $this->mount($this->preOrder->id);
    }

    public function resetPreOrder() {
        $this->reset();
        $this->mount();
        $this->alert('success', 'Form Reset Successfully');
    }
}
