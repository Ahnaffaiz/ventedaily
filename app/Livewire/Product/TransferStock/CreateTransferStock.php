<?php

namespace App\Livewire\Product\TransferStock;

use App\Enums\KeepStatus;
use App\Enums\KeepType;
use App\Enums\StockType;
use App\Models\Customer;
use App\Models\Group;
use App\Models\Keep;
use App\Models\KeepProduct;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Setting;
use App\Models\TransferProductStock;
use App\Models\TransferStock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

class CreateTransferStock extends Component
{
    use LivewireAlert;

    public string $subtitle = 'Transfer Stock';
    public string $subRoute = 'transfer-stock';

    public $productStockList, $product_id, $productStock, $products, $total_items;

    public $transferStock;

    #[Rule(['required'])]
    public $transfer_from = StockType::HOME_STOCK, $transfer_to = StockType::STORE_STOCK;

    public $cart = [];

    public $isOpen = false;

    public $isEdit = false;

    #[Title('Create Transfer Stock')]
    #[Layout('layouts.app')]

    protected $listeners = [
        'deleteProductStock'
    ];

    public function mount($transferstock = null)
    {
        View::share('subtitle', $this->subtitle);
        View::share('subRoute', $this->subRoute);
        $this->products = Product::all()->pluck('name', 'id')->toArray();
        if($transferstock) {
            $this->transferStock = TransferStock::where('id', $transferstock)->first();
            if($this->transferStock) {
                $this->edit();
                $this->getTotalItem();
            } else {
                return redirect()->route('transfer-stock')->with('error', 'Transfer Stock Not Found');
            }
        }
    }

    public function render()
    {
        return view('livewire.product.transfer-stock.create-transfer-stock')->with('subtitle', $this->subtitle);
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->product_id = null;
        $this->productStockList = null;
        $this->isOpen = false;
    }

    public function updatedTransferFrom()
    {
        $this->cart = null;
        if($this->isEdit) {
            if($this->transfer_from === $this->transferStock->transfer_from && $this->transfer_to === $this->transferStock->transfer_to) {
                $this->edit();
            } else {
                $this->cart = null;
            }
        }

        if($this->transfer_from == StockType::HOME_STOCK) {
            $this->transfer_to = StockType::STORE_STOCK;
        } elseif( $this->transfer_from == StockType::STORE_STOCK) {
            $this->transfer_to = StockType::HOME_STOCK;
        } elseif ($this->transfer_from == StockType::PRE_ORDER_STOCK) {
            $this->transfer_to = StockType::HOME_STOCK;
        }
    }

    public function updatedTransferTo()
    {
        if($this->transfer_to == $this->transfer_from && $this->transfer_to == StockType::HOME_STOCK) {
            $this->cart = null;
            $this->transfer_from = StockType::STORE_STOCK;
        } elseif( $this->transfer_to == $this->transfer_from && $this->transfer_to == StockType::STORE_STOCK) {
            $this->cart = null;
            $this->transfer_from = StockType::HOME_STOCK;
        } elseif ($this->transfer_to == $this->transfer_from && $this->transfer_to == StockType::PRE_ORDER_STOCK) {
            $this->cart = null;
            $this->transfer_from = StockType::HOME_STOCK;
        }
    }

    public function updatedStockTo()
    {
        if($this->transfer_to == $this->transfer_from && $this->transfer_to == 'home_stock') {
            $this->transfer_from = 'store_stock';
        } elseif( $this->transfer_to == $this->transfer_from && $this->transfer_to == 'store_stock') {
            $this->transfer_from = 'home_stock';
        } elseif ($this->transfer_to == $this->transfer_from && $this->transfer_to == 'pre_order_stock') {
            $this->transfer_from = 'home_stock';
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

    public function updatedProductId()
    {
        $this->productStockList = ProductStock::with('color', 'size')->where('product_id', $this->product_id)->get()->toArray();
        $this->productStockList = collect($this->productStockList)->map(function ($stockItem) {
            $cartItem = collect($this->transferStock?->transferProducts->keyBy('product_stock_id')->toArray())->firstWhere('product_stock_id', $stockItem['id']);
            if ($cartItem) {
                $stockItem[$this->transfer_from] += $cartItem['stock'];
            }
            return $stockItem;
        })->toArray();
    }

    public function getTotalItem()
    {
        $this->total_items = array_sum(array_column($this->cart, 'stock'));
    }

    public function addToCart($productStockId)
    {
        $productStock = ProductStock::where('id', $productStockId)->first();
        if(!array_key_exists('max_stock', $this->cart[$productStockId])) {
            $this->cart[$productStockId]['max_stock'] = $productStock->$this->transfer_from;
        }

        if($this->cart[$productStockId]['stock'] > $this->cart[$productStockId]['max_stock']) {
            $this->cart[$productStockId]['stock'] = $this->cart[$productStockId]['max_stock'];
            $this->alert('warning', 'Stock Not Enough');
        } elseif($this->cart[$productStockId]['stock'] == null) {
            $this->cart[$productStockId]['stock'] = 0;
            $this->productStock = $this->cart[$productStockId]['id'];
            $this->deleteProductStock();
        } elseif($this->cart[$productStockId]['stock'] < 1) {
            $this->cart[$productStockId]['stock'] = 1;
        }

        if($this->cart[$productStockId]['stock'] >= 1 && $this->cart[$productStockId]['stock'] <= $this->cart[$productStockId]['max_stock']) {
            $this->cart[$productStockId] = [
                'id' => $productStock->id,
                'color' => $productStock->color->name,
                'size' => $productStock->size->name,
                'product' => $productStock->product->name,
                'stock' => $this->cart[$productStockId]['stock'],
                'max_stock' => $this->cart[$productStockId]['max_stock'],
            ];
            $this->getTotalItem();
        }
    }

    public function addProductStock($productStockId)
    {
        if (!isset($this->cart[$productStockId])) {
            $transferProductStocks = $this->transferStock?->transferProducts()->where('product_stock_id', $productStockId)->first();
            $productStock = ProductStock::where('id', $productStockId)->first();
            $this->cart[$productStockId]['max_stock'] = $productStock[$this->transfer_from] + $transferProductStocks?->stock;

            if ($this->cart[$productStockId]['max_stock'] > 0) {
                $this->cart[$productStockId]['stock'] = 1;
                $this->addToCart($productStockId);
            } else {
                $this->alert('warning', "Out Of Stock");
            }

        } else {
            if($this->cart[$productStockId]['max_stock'] > $this->cart[$productStockId]['stock']) {
                $this->cart[$productStockId]['stock']++;
                $this->addToCart($productStockId);
            } else {
                $this->alert('warning', "Out Of Stock");
            }
        }
    }

    public function removeProductStock($productStockId)
    {
        if (isset($this->cart[$productStockId]) && $this->cart[$productStockId]['stock'] > 1) {
            $this->cart[$productStockId]['stock']--;
            $this->addToCart($productStockId);
        } elseif(isset($this->cart[$productStockId]) && $this->cart[$productStockId]['stock'] == 1) {
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
        $this->getTotalItem();
        $this->productStock = null;
        $this->alert('success', 'Product Successfully Deleted');
    }

    public function createTransferProductStock($transferStockId)
    {
        foreach ($this->cart as $productStock) {
            $stock = ProductStock::where('id', $productStock['id'])->first();
            if ($productStock['max_stock'] < $productStock['stock']) {
                $this->alert('warning', 'Stock Not Enough');
            } else {
                $stock->update([
                    $this->transfer_from => $stock[$this->transfer_from] - $productStock['stock'],
                    $this->transfer_to => $stock[$this->transfer_to] + $productStock['stock'],
                ]);
            }
            TransferProductStock::create([
                'transfer_stock_id' => $transferStockId,
                'product_stock_id' => $productStock['id'],
                'stock' => $productStock['stock'],
            ]);
        }
    }

    public function save()
    {
        $this->validate();
        try {
            $transferStock = TransferStock::create([
                'user_id' => Auth::user()->id,
                'transfer_from' => strtolower($this->transfer_from),
                'transfer_to' => strtolower($this->transfer_to),
                'total_items' => $this->total_items,
            ]);

            $this->createTransferProductStock($transferStock->id);
            $this->reset();
            $this->alert('success', 'Transfer Succesfully Created');
            return redirect()->route('transfer-stock');
        } catch (\Throwable $th) {
            $this->alert('warning', $th->getMessage());
        }
    }

    public function edit()
    {
        $this->isEdit = true;
        $this->transfer_from = $this->transferStock->transfer_from;
        $this->transfer_to = $this->transferStock->transfer_to;
        foreach ($this->transferStock->transferProducts as $transferProduct) {
            $this->cart[$transferProduct->product_stock_id] = [
                'id' => $transferProduct->product_stock_id,
                'color' => $transferProduct->productStock->color->name,
                'size' => $transferProduct->productStock->size->name,
                'product' => $transferProduct->productStock->product->name,
                'stock' => $transferProduct->stock,
                'max_stock' => $transferProduct->stock + $transferProduct->productStock[$this->transfer_from],
            ];
        }
    }

    public function update()
    {
        $this->validate();
        foreach ($this->transferStock->transferProducts as $transferProduct) {
            $productStock = ProductStock::where('id', $transferProduct->product_stock_id)->first();
            $productStock->update([
                $this->transferStock->transfer_from => $productStock[$this->transferStock->transfer_from] + $transferProduct->stock,
                $this->transferStock->transfer_to => $productStock[$this->transferStock->transfer_to] - $transferProduct->stock,
            ]);
            $transferProduct->delete();
        }
        $this->transferStock->update([
            'user_id' => Auth::user()->id,
            'transfer_from' => $this->transfer_from,
            'transfer_to' => $this->transfer_to,
            'total_items' => $this->total_items,
        ]);
        $this->createTransferProductStock($this->transferStock->id);
        $this->alert('success', 'Transfer Stock Succesfully Updated');
        $this->mount($this->transferStock->id);
    }

    public function resetKeep() {
        $this->reset();
        $this->mount();
        $this->alert('success', 'Form Reset Successfully');
    }
}
