<?php

namespace App\Livewire\Keep;

use App\Enums\KeepType;
use App\Models\Customer;
use App\Models\Group;
use App\Models\Keep;
use App\Models\KeepProduct;
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

class CreateKeep extends Component
{
    use LivewireAlert;

    public string $subtitle = 'Keep';
    public string $subRoute = 'keep';

    public $customers, $groups;
    public $keep, $isEdit;
    public $productStockList, $product_id, $productStock;

    #[Rule('required')]
    public $group_id;

    #[Rule('required')]
    public $customer_id;

    #[Rule('required')]
    public $keep_type;

    #[Rule('required')]
    public $keep_time;

    public $desc;
    public $cart = [], $total_items, $total_price;

    public $isOpen = false;

    #[Title('Create Keep')]
    #[Layout('layouts.app')]

    protected $listeners = [
        'deleteProductStock'
    ];

    public function mount($keep = null)
    {
        View::share('subtitle', $this->subtitle);
        View::share('subRoute', $this->subRoute);

        if($keep) {
            $this->keep = Keep::where('id', $keep)->first();
            $this->edit();
            $this->getTotalPrice();
        } else {
            $this->groups = Group::all()->pluck('name', 'id')->toArray();
        }
    }

    public function render()
    {
        return view('livewire.keep.create-keep')->with('subtitle', $this->subtitle);
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function updatedGroupId()
    {
        $this->customers = Customer::where('group_id', $this->group_id)->pluck('name', 'id')->toArray();
    }

    public function updatedKeepType()
    {
        if (strtolower($this->keep_type) === KeepType::REGULAR) {
            $setting = Setting::first();
            if($setting?->keep_timeout == null) {
                $this->alert('warning', 'Keep Timeout Belum diatur');
                $this->keep_type = null;
            } else {
                $keepTimeout = Setting::first()->keep_timeout;
                $this->keep_time = Carbon::tomorrow()->setTimeFromTimeString($keepTimeout);
            }
        } else {
            $this->keep_time = null;
        }
    }

    public function updatedProductId()
    {
        $this->productStockList = ProductStock::where('product_id', $this->product_id)->get();
    }

    public function getTotalPrice()
    {
        $this->total_items = array_sum(array_column($this->cart, 'quantity'));
        $this->total_price = array_sum(array_column($this->cart, 'total_price'));
    }

    public function addToCart($productStockId)
    {
        $productStock = ProductStock::where('id', $productStockId)->first();
        $this->cart[$productStockId]['selling_price'] = $productStock->purchase_price;
        if($productStock->home_stock > 0)
        $this->cart[$productStockId] = [
            'id' => $productStock->id,
            'color' => $productStock->color->name,
            'size' => $productStock->size->name,
            'product' => $productStock->product->name,
            'quantity' => $this->cart[$productStockId]['quantity'],
            'stock' => $this->cart[$productStockId]['stock'],
            'purchase_price' => $productStock->purchase_price,
            'selling_price' => $productStock->selling_price,
            'total_price' => $this->cart[$productStockId]['selling_price'] * $this->cart[$productStockId]['quantity']
        ];
        $this->getTotalPrice();
    }

    public function addProductStock($productStockId)
    {
        if (!isset($this->cart[$productStockId])) {
            $this->cart[$productStockId]['stock'] = ProductStock::where('id', $productStockId)->first()->home_stock;
            if ($this->cart[$productStockId]['stock'] > 0) {
                $this->cart[$productStockId]['quantity'] = 1;
                $this->addToCart($productStockId);
            } else {
                $this->alert('warning', "Out Of Stock");
            }
        } else {
            if($this->cart[$productStockId]['stock'] > $this->cart[$productStockId]['quantity']) {
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
        $this->alert('success', 'Product Successfully Deleted');
    }

    public function save()
    {
        $this->validate();
        $setting = Setting::first();
        try {
            $keep = Keep::create([
                'user_id' => Auth::user()->id,
                'no_keep' => $setting->keep_code . str_pad($setting->keep_increment + 1, 4, '0', STR_PAD_LEFT),
                'customer_id' => $this->customer_id,
                'total_price' => $this->total_price,
                'total_items' => $this->total_items,
                'keep_time' => $this->keep_time,
                'desc' => $this->desc,
            ]);

            $setting->update([
                'keep_increment' => $setting->keep_increment + 1
            ]);

            foreach ($this->cart as $productStock) {
                $keepProduct = KeepProduct::create([
                    'keep_id' => $keep->id,
                    'product_stock_id' => $productStock['id'],
                    'total_items' => $productStock['quantity'],
                    'selling_price' => $productStock['selling_price'],
                    'purchase_price' => $productStock['purchase_price'],
                    'total_price' => $productStock['total_price']
                ]);
                $stock = ProductStock::where('id', $productStock['id'])->first();
                $stock->update([
                    'home_stock' => $stock->home_stock-$productStock['quantity'],
                    'all_stock' => $stock->all_stock-$productStock['quantity'],
                ]);
            }

            $this->reset();
            $this->alert('success', 'Keep Order Succesfully Created');
            $this->mount();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function edit()
    {
        $this->isEdit = true;
        $this->group_id = $this->keep->customer->group_id;
        $this->customers = Customer::where('group_id', $this->group_id)->pluck('name', 'id')->toArray();
        $this->customer_id = $this->keep->customer_id;
        $this->keep_type = $this->keep->keep_type->key;
        $this->keep_time = $this->keep->keep_time;

        foreach ($this->keep->keepProducts as $keepProduct) {
            $this->cart[$keepProduct->product_stock_id] = [
                'id' => $keepProduct->product_stock_id,
                'color' => $keepProduct->productStock->color->name,
                'size' => $keepProduct->productStock->size->name,
                'product' => $keepProduct->productStock->product->name,
                'quantity' => $keepProduct->total_items,
                'stock' => $keepProduct->productStock->home_stock,
                'purchase_price' => $keepProduct->purchase_price,
                'selling_price' => $keepProduct->selling_price,
                'total_price' => $keepProduct->total_price
            ];
        }
    }

    public function update()
    {
        $this->validate();
        $this->keep->update([
            'user_id' => Auth::user()->id,
            'customer_id' => $this->customer_id,
            'total_price' => $this->total_price,
            'total_items' => $this->total_items,
            'keep_time' => $this->keep_time,
            'desc' => $this->desc,
        ]);

        foreach ($this->keep->keepProducts as $keepProduct) {
            $stock = ProductStock::where('id', $keepProduct->product_stock_id)->first();
            $stock->update([
                'home_stock' => $stock->home_stock+$keepProduct->total_items,
                'all_stock' => $stock->all_stock+$keepProduct->total_items,
            ]);
            $keepProduct->delete();
        }

        foreach ($this->cart as $productStock) {
            $keepProduct = KeepProduct::create([
                'keep_id' => $this->keep->id,
                'product_stock_id' => $productStock['id'],
                'total_items' => $productStock['quantity'],
                'selling_price' => $productStock['selling_price'],
                'purchase_price' => $productStock['purchase_price'],
                'total_price' => $productStock['total_price']
            ]);
            $stock = ProductStock::where('id', $productStock['id'])->first();
            $stock->update([
                'home_stock' => $stock->home_stock-$productStock['quantity'],
                'all_stock' => $stock->all_stock-$productStock['quantity'],
            ]);
        }

        $this->alert('success', 'Purchase Order Succesfully Updated');
        $this->mount();
    }
}
