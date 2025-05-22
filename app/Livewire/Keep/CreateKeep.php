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
use App\Models\Marketplace;
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
    public $order_id_marketplace, $marketplace_id, $marketplaces;

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
        $this->marketplaces = Marketplace::all()->pluck('name', 'id')->toArray();
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

        // Clear order_id_marketplace if group is not Online (group_id != 2)
        if ($this->group_id != 2) {
            $this->order_id_marketplace = null;
            $this->marketplace_id = null;
        }
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
            // Clear order_id_marketplace if not an Online customer
            if ($this->group_id != 2) {
                $this->order_id_marketplace = null;
                $this->marketplace_id = null;
            }

            $keep = Keep::create([
                'user_id' => Auth::user()->id,
                'status' => strtolower(KeepStatus::ACTIVE),
                'no_keep' => $setting->keep_code . str_pad($setting->keep_increment + 1, 4, '0', STR_PAD_LEFT),
                'customer_id' => $this->customer_id,
                'total_price' => $this->total_price,
                'keep_type' => strtolower($this->keep_type),
                'total_items' => $this->total_items,
                'keep_time' => $this->keep_time,
                'marketplace_id' => $this->marketplace_id,
                'order_id_marketplace' => $this->order_id_marketplace,
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
        $this->marketplace_id = $this->keep->marketplace_id;
        $this->order_id_marketplace = $this->keep->order_id_marketplace;
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

        // Clear order_id_marketplace if not an Online customer
        if ($this->group_id != 2) {
            $this->order_id_marketplace = null;
            $this->marketplace_id = null;
        }

        $this->keep->update([
            'user_id' => Auth::user()->id,
            'status' => strtolower(KeepStatus::ACTIVE),
            'customer_id' => $this->customer_id,
            'total_price' => $this->total_price,
            'total_items' => $this->total_items,
            'keep_type' => strtolower($this->keep_type),
            'keep_time' => $this->keep_time,
            'marketplace_id' => $this->marketplace_id,
            'order_id_marketplace' => $this->order_id_marketplace,
            'desc' => $this->desc,
        ]);

        // Get existing keepProducts indexed by product_stock_id for easy lookup
        $existingKeepProducts = $this->keep->keepProducts->keyBy('product_stock_id');
        $isReseller = $this->group_id == 1; // Group ID 1 is Reseller, 2 is Online
        $primaryStockType = $isReseller ? 'store_stock' : 'home_stock';
        $secondaryStockType = $isReseller ? 'home_stock' : 'store_stock';
        $processedItems = [];

        // Process items in the cart
        foreach ($this->cart as $productStockId => $cartItem) {
            $processedItems[] = $productStockId;
            $keepProduct = $existingKeepProducts->get($productStockId);
            $productStock = ProductStock::where('id', $productStockId)->first();

            // Check if this product already exists in the keep
            if ($keepProduct) {
                // 1. If KeepProduct already exists and quantity has changed
                if ($keepProduct->total_items != $cartItem['quantity']) {

                        // Check if there are any transfers associated with this keep product
                        $hasTransferredStock = TransferProductStock::whereJsonContains('keep_product_id', $keepProduct->id)->exists();

                        // Calculate the difference between current and new quantity
                        $quantityDifference = $cartItem['quantity'] - $keepProduct->total_items;
                        // Get current stocks from KeepProduct
                        $currentHomeStock = $keepProduct->home_stock;
                        $currentStoreStock = $keepProduct->store_stock;

                        // Smart allocation based on customer group
                        $isReseller = $this->group_id == 1; // Group ID 1 is Reseller, 2 is Online

                        if ($quantityDifference < 0) {
                            // Decreasing quantity - remove stock based on customer preference
                            $decreaseAmount = abs($quantityDifference);
                            $newHomeStock = $currentHomeStock;
                            $newStoreStock = $currentStoreStock;

                            if ($isReseller) {
                                // For reseller, decrease from home_stock first, then store_stock
                                if ($newHomeStock >= $decreaseAmount) {
                                    $newHomeStock -= $decreaseAmount;
                                } else {
                                    // If home_stock is not enough, take remaining from store_stock
                                    $remainingDecrease = $decreaseAmount - $newHomeStock;
                                    $newHomeStock = 0;
                                    $newStoreStock -= $remainingDecrease;
                                }
                            } else {
                                // For online, decrease from store_stock first, then home_stock
                                if ($newStoreStock >= $decreaseAmount) {
                                    $newStoreStock -= $decreaseAmount;
                                } else {
                                    // If store_stock is not enough, take remaining from home_stock
                                    $remainingDecrease = $decreaseAmount - $newStoreStock;
                                    $newStoreStock = 0;
                                    $newHomeStock -= $remainingDecrease;
                                }
                            }

                            // Calculate how much stock to return to ProductStock
                            $homeStockDifference = $currentHomeStock - $newHomeStock;
                            $storeStockDifference = $currentStoreStock - $newStoreStock;

                            // Return stock to product stock (regardless of whether it has transfers)
                            $productStock->update([
                                'home_stock' => $productStock->home_stock + $homeStockDifference,
                                'store_stock' => $productStock->store_stock + $storeStockDifference,
                                'all_stock' => $productStock->all_stock + $decreaseAmount
                            ]);

                            // Create stock history records
                            if ($homeStockDifference > 0) {
                                setStockHistory(
                                    $productStock->id,
                                    StockActivity::KEEP,
                                    StockStatus::CHANGE_REMOVE,
                                    StockType::HOME_STOCK,
                                    NULL,
                                    $homeStockDifference,
                                    $this->keep->no_keep,
                                    $productStock->all_stock,
                                    $productStock->home_stock,
                                    $productStock->store_stock,
                                    $productStock->pre_order_stock,
                                    true
                                );
                            }

                            if ($storeStockDifference > 0) {
                                setStockHistory(
                                    $productStock->id,
                                    StockActivity::KEEP,
                                    StockStatus::CHANGE_REMOVE,
                                    StockType::STORE_STOCK,
                                    NULL,
                                    $storeStockDifference,
                                    $this->keep->no_keep,
                                    $productStock->all_stock,
                                    $productStock->home_stock,
                                    $productStock->store_stock,
                                    $productStock->pre_order_stock,
                                    true
                                );
                            }

                            // Update the keepProduct with new values
                            $keepProduct->update([
                                'total_items' => $cartItem['quantity'],
                                'home_stock' => $newHomeStock,
                                'store_stock' => $newStoreStock,
                                'selling_price' => $cartItem['selling_price'],
                                'total_price' => $cartItem['total_price']
                            ]);
                        } else if ($quantityDifference > 0) {
                            // Increasing quantity

                            // Return old stock to ProductStock first
                            $productStock->update([
                                'home_stock' => $productStock->home_stock + $currentHomeStock,
                                'store_stock' => $productStock->store_stock + $currentStoreStock,
                                'all_stock' => $productStock->all_stock + $keepProduct->total_items
                            ]);

                            // Create stock history records for returning
                            if ($currentHomeStock > 0) {
                                setStockHistory(
                                    $productStock->id,
                                    StockActivity::KEEP,
                                    StockStatus::CHANGE_REMOVE,
                                    StockType::HOME_STOCK,
                                    NULL,
                                    $currentHomeStock,
                                    $this->keep->no_keep,
                                    $productStock->all_stock,
                                    $productStock->home_stock,
                                    $productStock->store_stock,
                                    $productStock->pre_order_stock,
                                    true
                                );
                            }

                            if ($currentStoreStock > 0) {
                                setStockHistory(
                                    $productStock->id,
                                    StockActivity::KEEP,
                                    StockStatus::CHANGE_REMOVE,
                                    StockType::STORE_STOCK,
                                    NULL,
                                    $currentStoreStock,
                                    $this->keep->no_keep,
                                    $productStock->all_stock,
                                    $productStock->home_stock,
                                    $productStock->store_stock,
                                    $productStock->pre_order_stock,
                                    true
                                );
                            }

                            // Now allocate the new total quantity from ProductStock
                            $primaryStockType = $isReseller ? 'store_stock' : 'home_stock';
                            $secondaryStockType = $isReseller ? 'home_stock' : 'store_stock';

                            $stockAllocation = $this->allocateStockBasedOnGroup(
                                $productStock,
                                $cartItem['quantity'],
                                $primaryStockType,
                                $secondaryStockType
                            );

                            // Update the keepProduct with new allocation
                            $keepProduct->update([
                                'total_items' => $cartItem['quantity'],
                                'home_stock' => $stockAllocation['home_stock'],
                                'store_stock' => $stockAllocation['store_stock'],
                                'selling_price' => $cartItem['selling_price'],
                                'total_price' => $cartItem['total_price']
                            ]);

                            // Update ProductStock with remaining stock
                            $productStock->update([
                                'home_stock' => $stockAllocation['remaining_home_stock'],
                                'store_stock' => $stockAllocation['remaining_store_stock'],
                                'all_stock' => $stockAllocation['remaining_home_stock'] + $stockAllocation['remaining_store_stock']
                            ]);

                            // Create stock history records for new allocation
                            if ($stockAllocation['home_stock'] > 0) {
                                setStockHistory(
                                    $productStock->id,
                                    StockActivity::KEEP,
                                    StockStatus::CHANGE_ADD,
                                    StockType::HOME_STOCK,
                                    NULL,
                                    $stockAllocation['home_stock'],
                                    $this->keep->no_keep,
                                    $productStock->all_stock,
                                    $productStock->home_stock,
                                    $productStock->store_stock,
                                    $productStock->pre_order_stock,
                                    true
                                );
                            }

                            if ($stockAllocation['store_stock'] > 0) {
                                setStockHistory(
                                    $productStock->id,
                                    StockActivity::KEEP,
                                    StockStatus::CHANGE_ADD,
                                    StockType::STORE_STOCK,
                                    NULL,
                                    $stockAllocation['store_stock'],
                                    $this->keep->no_keep,
                                    $productStock->all_stock,
                                    $productStock->home_stock,
                                    $productStock->store_stock,
                                    $productStock->pre_order_stock,
                                    true
                                );
                            }
                        } else {
                            // Quantity hasn't changed, just update pricing if needed
                            $keepProduct->update([
                                'selling_price' => $cartItem['selling_price'],
                                'total_price' => $cartItem['total_price']
                            ]);
                        }
                    }
            } else {
                // 2. & 3. If KeepProduct doesn't exist, create a new one with stock based on customer group
                $stockAllocation = $this->allocateStockBasedOnGroup(
                    $productStock,
                    $cartItem['quantity'],
                    $primaryStockType,
                    $secondaryStockType
                );

                // Create new KeepProduct
                KeepProduct::create([
                    'keep_id' => $this->keep->id,
                    'product_stock_id' => $productStockId,
                    'total_items' => $cartItem['quantity'],
                    'home_stock' => $stockAllocation['home_stock'],
                    'store_stock' => $stockAllocation['store_stock'],
                    'selling_price' => $cartItem['selling_price'],
                    'purchase_price' => $productStock->purchase_price,
                    'total_price' => $cartItem['total_price']
                ]);

                // Update ProductStock based on the allocation
                $productStock->update([
                    'home_stock' => $stockAllocation['remaining_home_stock'],
                    'store_stock' => $stockAllocation['remaining_store_stock'],
                    'all_stock' => $stockAllocation['remaining_home_stock'] + $stockAllocation['remaining_store_stock']
                ]);

                // Create stock history records
                if ($stockAllocation['home_stock'] > 0) {
                    setStockHistory(
                        $productStock->id,
                        StockActivity::KEEP,
                        StockStatus::ADD,
                        StockType::HOME_STOCK,
                        NULL,
                        $stockAllocation['home_stock'],
                        $this->keep->no_keep,
                        $productStock->all_stock,
                        $productStock->home_stock,
                        $productStock->store_stock,
                        $productStock->pre_order_stock,
                        true
                    );
                }

                if ($stockAllocation['store_stock'] > 0) {
                    setStockHistory(
                        $productStock->id,
                        StockActivity::KEEP,
                        StockStatus::ADD,
                        StockType::STORE_STOCK,
                        NULL,
                        $stockAllocation['store_stock'],
                        $this->keep->no_keep,
                        $productStock->all_stock,
                        $productStock->home_stock,
                        $productStock->store_stock,
                        $productStock->pre_order_stock,
                        true
                    );
                }
            }
        }

        // Handle items that were removed from the cart
        foreach ($existingKeepProducts as $productStockId => $keepProduct) {
            if (!in_array($productStockId, $processedItems)) {
                $productStock = ProductStock::where('id', $productStockId)->first();

                // Check if this keep product has any transfers
                $transferProductStocks = TransferProductStock::whereJsonContains('keep_product_id', $keepProduct->id)
                    ->get();

                if ($transferProductStocks->isNotEmpty()) {
                    // Remove keep_product_id from TransferProductStock records
                    foreach ($transferProductStocks as $transferProductStock) {
                        $keepProductIds = $transferProductStock->keep_product_id ?? [];

                        if (is_array($keepProductIds)) {
                            $keepProductIds = array_filter($keepProductIds, function ($id) use ($keepProduct) {
                                return $id != $keepProduct->id;
                            });

                            $transferProductStock->update([
                                'keep_product_id' => array_values($keepProductIds)
                            ]);
                        }
                    }
                }

                // Return stock to product_stock
                $productStock->update([
                    'home_stock' => $productStock->home_stock + $keepProduct->home_stock,
                    'store_stock' => $productStock->store_stock + $keepProduct->store_stock,
                    'all_stock' => $productStock->all_stock + $keepProduct->total_items
                ]);

                if ($keepProduct->home_stock > 0) {
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

                if ($keepProduct->store_stock > 0) {
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

                // Delete the keep product
                $keepProduct->delete();
            }
        }

        $this->alert('success', 'Keep Successfully Updated');
        $this->mount($this->keep->id);
    }

    /**
     * Allocate stock based on customer group
     *
     * @param ProductStock $productStock
     * @param int $quantity
     * @param string $primaryStockType
     * @param string $secondaryStockType
     * @return array
     */
    private function allocateStockBasedOnGroup($productStock, $quantity, $primaryStockType, $secondaryStockType)
    {
        $allocation = [
            'home_stock' => 0,
            'store_stock' => 0,
            'remaining_home_stock' => $productStock->home_stock,
            'remaining_store_stock' => $productStock->store_stock
        ];

        // Try to take from primary stock type first
        $primaryStockAvailable = $productStock->$primaryStockType;
        if ($primaryStockAvailable >= $quantity) {
            // If primary stock is sufficient
            $allocation[$primaryStockType] = $quantity;
            $allocation['remaining_' . $primaryStockType] = $primaryStockAvailable - $quantity;
        } else {
            // If primary stock is not sufficient, take what's available
            $allocation[$primaryStockType] = $primaryStockAvailable;
            $allocation['remaining_' . $primaryStockType] = 0;

            // Then take the rest from secondary stock
            $remainingNeeded = $quantity - $primaryStockAvailable;
            $secondaryStockAvailable = $productStock->$secondaryStockType;

            if ($secondaryStockAvailable >= $remainingNeeded) {
                $allocation[$secondaryStockType] = $remainingNeeded;
                $allocation['remaining_' . $secondaryStockType] = $secondaryStockAvailable - $remainingNeeded;
            } else {
                // If even secondary stock is not enough (this shouldn't happen with proper validation)
                $allocation[$secondaryStockType] = $secondaryStockAvailable;
                $allocation['remaining_' . $secondaryStockType] = 0;
            }
        }

        return $allocation;
    }

    public function resetKeep() {
        $this->reset();
        $this->mount();
        $this->alert('success', 'Form Reset Successfully');
    }
}
