<?php

namespace App\Livewire\Keep;

use App\Enums\KeepStatus;
use App\Enums\KeepType;
use App\Models\Customer;
use App\Models\Group;
use App\Models\Keep;
use App\Models\KeepProduct;
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

class CreateKeep extends Component
{
    use LivewireAlert;

    public string $subtitle = 'Keep';
    public string $subRoute = 'keep';

    public $customers, $groups;
    public $keep, $isEdit;
    public $productStockList, $product_id, $productStock, $products;

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
        $this->groups = Group::all()->pluck('name', 'id')->toArray();
        $this->products = Product::all()->pluck('name', 'id')->toArray();
        if($keep) {
            $this->keep = Keep::where('id', $keep)->first();
            if(strtolower($this->keep->status) === strtolower(KeepStatus::ACTIVE)) {
                $this->edit();
                $this->getTotalPrice();
            } else {
                return redirect()->route('keep')->with('error', 'Keep Order Not Found');
            }
        } else {
            $keepTimeout = Setting::first()->keep_timeout;
            $this->keep_type = KeepType::REGULAR;
            $this->keep_time = Carbon::tomorrow()->setTimeFromTimeString($keepTimeout);
        }
    }

    public function render()
    {
        return view('livewire.keep.create-keep')->with('subtitle', $this->subtitle);
    }

    public function openModal()
    {
        $this->validate();
        if($this->group_id == null) {
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

    public function updatedGroupId()
    {
        $this->customers = Customer::where('group_id', $this->group_id)->pluck('name', 'id')->toArray();
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
        $this->productStockList = ProductStock::with('color', 'size')->where('product_id', $this->product_id)->get()->toArray();

        $this->productStockList = collect($this->productStockList)->map(function ($stockItem) {
            $cartItem = collect($this->cart)->firstWhere('id', $stockItem['id']);
            if ($cartItem) {
                $stockItem['all_stock'] += $cartItem['all_stock'];
                $stockItem['home_stock'] += $cartItem['home_stock'];
                $stockItem['store_stock'] += $cartItem['store_stock'];
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
        if(!array_key_exists('home_stock', $this->cart[$productStockId])) {
            $this->cart[$productStockId]['home_stock'] = ProductStock::where('id', $productStockId)->first()->home_stock;
            $this->cart[$productStockId]['store_stock'] = ProductStock::where('id', $productStockId)->first()->store_stock;
            $this->cart[$productStockId]['all_stock'] = ProductStock::where('id', $productStockId)->first()->all_stock;
        }

        if($this->cart[$productStockId]['quantity'] > $this->cart[$productStockId]['all_stock']) {
            $this->cart[$productStockId]['quantity'] = $this->cart[$productStockId]['all_stock'];
            $this->alert('warning', 'Stock Not Enough');
        } elseif($this->cart[$productStockId]['quantity'] == null) {
            $this->cart[$productStockId]['quantity'] = 0;
            $this->productStock = $this->cart[$productStockId]['id'];
            $this->deleteProductStock();
        } elseif($this->cart[$productStockId]['quantity'] < 1) {
            $this->cart[$productStockId]['quantity'] = 1;
        }

        if($this->cart[$productStockId]['quantity'] >= 1 && $this->cart[$productStockId]['quantity'] <= $this->cart[$productStockId]['all_stock']) {
            $this->cart[$productStockId] = [
                'id' => $productStock->id,
                'color' => $productStock->color->name,
                'size' => $productStock->size->name,
                'product' => $productStock->product->name,
                'quantity' => $this->cart[$productStockId]['quantity'],
                'home_stock' => $this->cart[$productStockId]['home_stock'],
                'store_stock' => $this->cart[$productStockId]['store_stock'],
                'all_stock' => $this->cart[$productStockId]['all_stock'],
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
            $this->cart[$productStockId]['home_stock'] = ProductStock::where('id', $productStockId)->first()->home_stock;
            $this->cart[$productStockId]['store_stock'] = ProductStock::where('id', $productStockId)->first()->store_stock;
            $this->cart[$productStockId]['all_stock'] = ProductStock::where('id', $productStockId)->first()->all_stock;

            if ($this->cart[$productStockId]['all_stock'] > 0) {
                $this->cart[$productStockId]['quantity'] = 1;
                $this->addToCart($productStockId);
            } else {
                $this->alert('warning', "Out Of Stock");
            }

        } else {
            if($this->cart[$productStockId]['all_stock'] > $this->cart[$productStockId]['quantity']) {
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
            $keep = Keep::create([
                'user_id' => Auth::user()->id,
                'status' => strtolower(KeepStatus::ACTIVE),
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
                $store_stock = 0;
                $home_stock = 0;
                $stock = ProductStock::where('id', $productStock['id'])->first();
                if ($this->group_id == 2) {
                    if ($productStock['home_stock'] < $productStock['quantity']) {
                        $store_stock = $productStock['quantity'] - $productStock['home_stock'];
                        $home_stock = $stock->home_stock;
                        $stock->update([
                            'store_stock' => $stock->store_stock - $store_stock,
                            'home_stock' => 0,
                            'all_stock' => $stock->store_stock - $store_stock,
                        ]);
                    } else {
                        $home_stock = $productStock['quantity'];
                        $stock->update([
                            'home_stock' => $stock->home_stock - $productStock['quantity'],
                            'all_stock' => $stock->home_stock - $productStock['quantity'] + $stock->store_stock,
                        ]);
                    }
                } elseif ($this->group_id == 1) {
                    if ($productStock['store_stock'] < $productStock['quantity']) {
                        $home_stock = $productStock['quantity'] - $productStock['store_stock'];
                        $store_stock = $stock->store_stock;
                        $stock->update([
                            'home_stock' => $stock->home_stock - $home_stock,
                            'store_stock' => 0,
                            'all_stock' => $stock->home_stock - $home_stock,
                        ]);
                    } else {
                        $store_stock = $productStock['quantity'];
                        $stock->update([
                            'store_stock' => $stock->store_stock - $productStock['quantity'],
                            'all_stock' => $stock->store_stock - $productStock['quantity'] + $stock->home_stock,
                        ]);
                    }
                }
                $keepProduct = KeepProduct::create([
                    'keep_id' => $keep->id,
                    'product_stock_id' => $productStock['id'],
                    'total_items' => $productStock['quantity'],
                    'home_stock' => $home_stock,
                    'store_stock' => $store_stock,
                    'selling_price' => $productStock['selling_price'],
                    'purchase_price' => $productStock['purchase_price'],
                    'total_price' => $productStock['total_price']
                ]);
            }

            $this->reset();
            $this->alert('success', 'Keep Order Succesfully Created');
            $this->mount();
        } catch (\Throwable $th) {
            $this->alert('warning', $th);
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
                'all_stock' => $keepProduct->home_stock + $keepProduct->store_stock + $keepProduct->productStock->all_stock,
                'home_stock' => $keepProduct->home_stock + $keepProduct->productStock->home_stock,
                'store_stock' => $keepProduct->store_stock + $keepProduct->productStock->store_stock,
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
            'status' => strtolower(KeepStatus::ACTIVE),
            'customer_id' => $this->customer_id,
            'total_price' => $this->total_price,
            'total_items' => $this->total_items,
            'keep_time' => $this->keep_time,
            'desc' => $this->desc,
        ]);

        foreach ($this->keep->keepProducts as $keepProduct) {
            $productStock = ProductStock::where('id', $keepProduct->product_stock_id)->first();
            $productStock->update([
                'home_stock' => $productStock['home_stock'] + $keepProduct->home_stock,
                'store_stock' => $productStock['store_stock'] + $keepProduct->store_stock,
            ]);
            $keepProduct->delete();
        }


        foreach ($this->cart as $productStock) {
            $store_stock = 0;
            $home_stock = 0;
            $stock = ProductStock::where('id', $productStock['id'])->first();
            if ($this->group_id == 2) {
                if ($productStock['home_stock'] < $productStock['quantity']) {
                    $store_stock = $productStock['quantity'] - $productStock['home_stock'];
                    $home_stock = $stock->home_stock;
                    $stock->update([
                        'store_stock' => $stock->store_stock - $store_stock,
                        'home_stock' => 0,
                        'all_stock' => $stock->store_stock - $store_stock,
                    ]);
                } else {
                    $home_stock = $productStock['quantity'];
                    $stock->update([
                        'home_stock' => $stock->home_stock - $productStock['quantity'],
                        'all_stock' => $stock->home_stock - $productStock['quantity'] + $stock->store_stock,
                    ]);
                }
            } elseif ($this->group_id == 1) {
                if ($productStock['store_stock'] < $productStock['quantity']) {
                    $home_stock = $productStock['quantity'] - $productStock['store_stock'];
                    $store_stock = $stock->store_stock;
                    $stock->update([
                        'home_stock' => $stock->home_stock - $home_stock,
                        'store_stock' => 0,
                        'all_stock' => $stock->home_stock - $home_stock,
                    ]);
                } else {
                    $store_stock = $productStock['quantity'];
                    $stock->update([
                        'store_stock' => $stock->store_stock - $productStock['quantity'],
                        'all_stock' => $stock->store_stock - $productStock['quantity'] + $stock->home_stock,
                    ]);
                }
            }
            $keepProduct = KeepProduct::create([
                'keep_id' => $this->keep->id,
                'product_stock_id' => $productStock['id'],
                'total_items' => $productStock['quantity'],
                'home_stock' => $home_stock,
                'store_stock' => $store_stock,
                'selling_price' => $productStock['selling_price'],
                'purchase_price' => $productStock['purchase_price'],
                'total_price' => $productStock['total_price']
            ]);
        }

        $this->alert('success', 'Purchase Order Succesfully Updated');
        $this->mount($this->keep->id);
    }

    public function resetKeep() {
        $this->reset();
        $this->mount();
        $this->alert('success', 'Form Reset Successfully');
    }
}
