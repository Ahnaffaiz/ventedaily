<?php

namespace App\Livewire\Keep;

use App\Enums\KeepStatus;
use App\Enums\KeepType;
use App\Enums\StockActivity;
use App\Enums\StockStatus;
use App\Enums\StockType;
use App\Models\Customer;
use App\Models\Group;
use App\Models\Keep;
use App\Models\KeepProduct;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Setting;
use App\Models\TransferProductStock;
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
    public $keep, $isEdit, $no_keep;
    public $productStockList, $product_id, $productStock, $products;

    #[Rule('required')]
    public $group_id;

    #[Rule('required')]
    public $customer_id;
    public $selectedCustomerLabel = null;

    #[Rule('required')]
    public $keep_type = KeepType::REGULAR;

    #[Rule('required')]
    public $keep_time;

    public $desc;
    public $cart = [], $total_items, $total_price;

    //history
    public $productStockHistories = [];

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
            if ($this->keep) {
                $this->no_keep = $this->keep->no_keep;
                if(strtolower($this->keep->status) === strtolower(KeepStatus::ACTIVE)) {
                    $this->edit();
                    $this->getTotalPrice();
                }
            } else {
                return redirect()->route('keep')->with('error', 'Keep Order Not Found');
            }
        } else {
            $setting = Setting::first();
            $keepTimeout = $setting->keep_timeout;
            $this->no_keep = $setting->keep_code . str_pad($setting->keep_increment + 1, 4, '0', STR_PAD_LEFT);
            $this->keep_type = strtolower(KeepType::REGULAR);
            if($this->keep_type == strtolower(KeepType::REGULAR)) {
                $this->keep_time = Carbon::tomorrow()->setTimeFromTimeString($keepTimeout);
            }
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

    public function searchCustomer($query)
    {
        $this->customers = Customer::where('group_id', $this->group_id)->pluck('name', 'id')->toArray();
        if ($query) {
            $this->customers = collect(Customer::where('group_id', $this->group_id)->pluck('name', 'id')->toArray())
                ->filter(function ($label, $value) use ($query) {
                    return stripos($label, $query) !== false;
                })
                ->toArray();
            }
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
        if (strtolower($this->keep_type) === strtolower(KeepType::REGULAR)) {
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
        $this->productStockList = ProductStock::with('color', 'size')
            ->where('product_id', $this->product_id)
            ->get()
            ->map(function ($stock) {
                $stock['total_stock'] = $stock['all_stock'] - $stock['pre_order_stock'];
                return $stock;
            })
            ->toArray();
        $this->productStockList = collect($this->productStockList)->map(function ($stockItem) {
            $cartItem = collect($this->keep?->keepProducts->keyBy('product_stock_id')->toArray())->firstWhere('product_stock_id', $stockItem['id']);
            if ($cartItem) {
                $stockItem['all_stock'] += $cartItem['total_items'];
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
            $this->cart[$productStockId]['home_stock'] = $productStock->home_stock;
            $this->cart[$productStockId]['store_stock'] = $productStock->store_stock;
            $this->cart[$productStockId]['total_stock'] = $productStock->home_stock + $productStock->store_stock;
        }

        if($this->cart[$productStockId]['quantity'] > $this->cart[$productStockId]['total_stock']) {
            $this->cart[$productStockId]['quantity'] = $this->cart[$productStockId]['total_stock'];
            $this->alert('warning', 'Stock Not Enough');
        } elseif($this->cart[$productStockId]['quantity'] == null) {
            $this->cart[$productStockId]['quantity'] = 0;
            $this->productStock = $this->cart[$productStockId]['id'];
            $this->deleteProductStock();
        } elseif($this->cart[$productStockId]['quantity'] < 1) {
            $this->cart[$productStockId]['quantity'] = 1;
        }

        if($this->cart[$productStockId]['quantity'] >= 1 && $this->cart[$productStockId]['quantity'] <= $this->cart[$productStockId]['total_stock']) {
            if (isset($this->cart[$productStockId])) {
                $this->cart[$productStockId] = array_merge(
                    $this->cart[$productStockId],
                    [
                        'color' => $productStock->color->name,
                        'size' => $productStock->size->name,
                        'product' => $productStock->product->name,
                        'purchase_price' => $productStock->purchase_price,
                        'selling_price' => $productStock->selling_price,
                        'total_price' => $this->cart[$productStockId]['selling_price'] * $this->cart[$productStockId]['quantity']
                    ]
                );
            }
            $this->getTotalPrice();
        }

    }

    public function addProductStock($productStockId)
    {
        if (!isset($this->cart[$productStockId])) {
            $keepProduct = $this->keep?->keepProducts()->where('product_stock_id', $productStockId)->first();
            $productStock = ProductStock::where('id', $productStockId)->first();
            $this->cart[$productStockId]['id'] = $productStock->id;
            $this->cart[$productStockId]['home_stock'] = $productStock->home_stock + $keepProduct?->home_stock;
            $this->cart[$productStockId]['store_stock'] = $productStock->store_stock + $keepProduct?->store_stock;
            $this->cart[$productStockId]['total_stock'] = $productStock->home_stock + $productStock->store_stock + $keepProduct?->total_items;
            $this->cart[$productStockId]['keep_product_id'] = null;
            $this->cart[$productStockId]['transfer'] = 0;

            if ($this->cart[$productStockId]['total_stock'] > 0) {
                $this->cart[$productStockId]['quantity'] = 1;
                $this->addToCart($productStockId);
            } else {
                $this->alert('warning', "Out Of Stock");
            }

        } else {
            if($this->cart[$productStockId]['total_stock'] > $this->cart[$productStockId]['quantity']) {
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
                'cancelButtonColor' => '#d33',
                'customClass' => [
                    'confirmButton' => 'btn bg-primary text-white hover:bg-primary-dark',
                    'cancelButton' => 'btn bg-danger text-white hover:bg-danger-dark'
                ]
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

    public function createKeepProduct($keep_id)
    {
        $keep = Keep::where('id', $keep_id)->first();
        foreach ($this->cart as $productStock) {
            $stock = ProductStock::where('id', $productStock['id'])->first();
            $stockType = $this->group_id == 2 ? 'home_stock' : 'store_stock';
            $notStockType = $this->group_id == 2 ? 'store_stock' : 'home_stock';
            $keepStock[$stockType] = 0;
            $keepStock[$notStockType] = 0;
            $stockStatus = $this->isEdit ? StockStatus::CHANGE_ADD : StockStatus::ADD;
            if ($productStock[$stockType] < $productStock['quantity']) {
                $keepStock[$stockType] = $stock->$stockType;
                $keepStock[$notStockType] = $productStock['quantity'] - $stock->$stockType;

                $stock->update([
                    $stockType => 0,
                    'all_stock' => $stock->all_stock - $keepStock[$stockType],
                ]);

                setStockHistory(
                    $stock->id,
                    StockActivity::KEEP,
                    $stockStatus,
                    NULL,
                    $stockType,
                    $keepStock[$stockType],
                    $keep->no_keep,
                    $stock->all_stock,
                    $stock->home_stock,
                    $stock->store_stock,
                    $stock->pre_order_stock,
                    true
                );

                $stock->update([
                    $notStockType => $stock->$notStockType - $keepStock[$notStockType],
                    'all_stock' => $stock->all_stock - $keepStock[$notStockType],
                ]);

                setStockHistory(
                    $stock->id,
                    StockActivity::KEEP,
                    $stockStatus,
                    NULL,
                    $notStockType,
                    $keepStock[$notStockType],
                    $keep->no_keep,
                    $stock->all_stock,
                    $stock->home_stock,
                    $stock->store_stock,
                    $stock->pre_order_stock,
                    true
                );

            } else {
                $keepStock[$stockType] = $productStock['quantity'];
                $stock->update([
                    $stockType => $stock->$stockType - $productStock['quantity'],
                    'all_stock' => $stock->all_stock - $productStock['quantity'],
                ]);

                setStockHistory(
                    $stock->id,
                    StockActivity::KEEP,
                    $stockStatus,
                    NULL,
                    $stockType,
                    $productStock['quantity'],
                    $keep->no_keep,
                    $stock->all_stock,
                    $stock->home_stock,
                    $stock->store_stock,
                    $stock->pre_order_stock,
                    true
                );
            }

            KeepProduct::create([
                'keep_id' => $keep_id,
                'product_stock_id' => $productStock['id'],
                'total_items' => $productStock['quantity'],
                $stockType => $keepStock[$stockType],
                $notStockType => $keepStock[$notStockType],
                'selling_price' => $productStock['selling_price'],
                'purchase_price' => $productStock['purchase_price'],
                'total_price' => $productStock['total_price']
            ]);
        }
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
                'keep_type' => strtolower($this->keep_type),
                'total_items' => $this->total_items,
                'keep_time' => $this->keep_time,
                'desc' => $this->desc,
            ]);

            $setting->update([
                'keep_increment' => $setting->keep_increment + 1
            ]);

            $this->createKeepProduct($keep->id);
            $this->reset();
            $this->alert('success', 'Keep Order Succesfully Created');
            return redirect()->route('keep');
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
        $this->selectedCustomerLabel = Customer::find($this->customer_id)?->name ?? '';
        $this->keep_type = $this->keep->keep_type->key;
        $this->keep_time = $this->keep->keep_time;
        foreach ($this->keep->keepProducts as $keepProduct) {
            $transferProductStock = TransferProductStock::whereJsonContains('keep_product_id', $keepProduct->id)
                    ->with('transferStock')
                    ->get()->sum('stock');
            $this->cart[$keepProduct->product_stock_id] = [
                'id' => $keepProduct->product_stock_id,
                'color' => $keepProduct->productStock->color->name,
                'size' => $keepProduct->productStock->size->name,
                'product' => $keepProduct->productStock->product->name,
                'quantity' => $keepProduct->total_items,
                'total_stock' => $keepProduct->total_items + $keepProduct->productStock->store_stock + $keepProduct->productStock->home_stock,
                'home_stock' => $keepProduct->home_stock + $keepProduct->productStock->home_stock,
                'store_stock' => $keepProduct->store_stock + $keepProduct->productStock->store_stock,
                'purchase_price' => $keepProduct->purchase_price,
                'selling_price' => $keepProduct->selling_price,
                'total_price' => $keepProduct->total_price,
                'keep_product_id' => $keepProduct->id,
                'transfer' => $transferProductStock > 0 ? $transferProductStock : 0
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
            'keep_type' => strtolower($this->keep_type),
            'keep_time' => $this->keep_time,
            'desc' => $this->desc,
        ]);

        foreach ($this->keep->keepProducts as $keepProduct) {
            $productStock = ProductStock::where('id', $keepProduct->product_stock_id)->first();
            $transferProductStock = TransferProductStock::whereJsonContains('keep_product_id', $keepProduct->id)
                    ->with('transferStock')
                    ->get();
            if($transferProductStock) {
                $fromCart = $this->cart[$keepProduct->product_stock_id];
                $keepProduct->update([
                    'total_items' => $fromCart['quantity'],
                    'home_stock' => $fromCart['home_stock'],
                    'store_stock' => $fromCart['store_stock'],
                ]);
                unset($this->cart[$keepProduct->product_stock_id]);
            } else {
                $productStock->update([
                    'home_stock' => $productStock['home_stock'] + $keepProduct->home_stock,
                    'store_stock' => $productStock['store_stock'] + $keepProduct->store_stock,
                    'all_stock' => $productStock['all_stock'] + $keepProduct->total_items
                ]);
                if($keepProduct->home_stock > 0) {
                    setStockHistory(
                        $productStock->id,
                        StockActivity::KEEP,
                        StockStatus::CHANGE_REMOVE,
                        StockType::HOME_STOCK,
                        NULL,
                        $keepProduct->home_stock,
                        $this->keep->no_keep,
                        $productStock->all_stock,
                        $productStock->home_stock,
                        $productStock->store_stock,
                        $productStock->pre_order_stock,
                        true
                    );
                }

                if($keepProduct->store_stock > 0) {
                    setStockHistory(
                        $productStock->id,
                        StockActivity::KEEP,
                        StockStatus::CHANGE_REMOVE,
                        StockType::STORE_STOCK,
                        NULL,
                        $keepProduct->store_stock,
                        $this->keep->no_keep,
                        $productStock->all_stock,
                        $productStock->home_stock,
                        $productStock->store_stock,
                        $productStock->pre_order_stock,
                        true
                    );
                }
                $keepProduct->delete();
            }
        }

        $this->createKeepProduct($this->keep->id);
        $this->productStockHistories = [];
        $this->alert('success', 'Purchase Order Succesfully Updated');
        $this->mount($this->keep->id);
    }

    public function resetKeep() {
        $this->reset();
        $this->mount();
        $this->alert('success', 'Form Reset Successfully');
    }
}
