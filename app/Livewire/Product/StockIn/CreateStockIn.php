<?php

namespace App\Livewire\Product\StockIn;

use App\Enums\StockActivity;
use App\Enums\StockStatus;
use App\Enums\StockType;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockIn;
use App\Models\StockInProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

class CreateStockIn extends Component
{

    use LivewireAlert;

    public string $subtitle = 'Stock In';
    public string $subRoute = 'stock-in';

    public $productStockList, $product_id, $productStock, $products, $total_items;

    public $stockIn;

    #[Rule(['required'])]
    public $stock_type = StockType::PRE_ORDER_STOCK;

    public $cart = [];

    public $isOpen = false;

    public $isEdit = false;

    #[Title('Create Stock In')]
    #[Layout('layouts.app')]

    protected $listeners = [
        'deleteProductStock'
    ];

    public function mount($stockin = null)
    {
        View::share('subtitle', $this->subtitle);
        View::share('subRoute', $this->subRoute);
        $this->products = Product::all()->pluck('name', 'id')->toArray();
        if($stockin) {
            $this->stockIn = StockIn::where('id', $stockin)->first();
            if($this->stockIn) {
                $this->edit();
                $this->getTotalItem();
            } else {
                return redirect()->route('stock-in')->with('error', 'Stock In Not Found');
            }
        }
    }

    public function render()
    {
        return view('livewire.product.stock-in.create-stock-in')->with('subtitle', $this->subtitle);
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
    }

    public function getTotalItem()
    {
        $this->total_items = array_sum(array_column($this->cart, 'stock'));
    }

    public function addToCart($productStockId)
    {
        $productStock = ProductStock::where('id', $productStockId)->first();

        if($this->cart[$productStockId]['stock'] == null) {
            $this->cart[$productStockId]['stock'] = 0;
            $this->productStock = $this->cart[$productStockId]['id'];
            $this->deleteProductStock();
        } elseif($this->cart[$productStockId]['stock'] < 1) {
            $this->cart[$productStockId]['stock'] = 1;
        }

        if($this->cart[$productStockId]['stock'] >= 1) {
            $this->cart[$productStockId] = [
                'id' => $productStock->id,
                'color' => $productStock->color->name,
                'size' => $productStock->size->name,
                'product' => $productStock->product->name,
                'stock' => $this->cart[$productStockId]['stock'],
            ];
            $this->getTotalItem();
        }
    }

    public function addProductStock($productStockId)
    {
        if (!isset($this->cart[$productStockId])) {
            $this->cart[$productStockId]['stock'] = 1;
            $this->addToCart($productStockId);
        } else {
            $this->cart[$productStockId]['stock']++;
            $this->addToCart($productStockId);
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
                'cancelButtonText' => 'Cancel',
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
        $this->getTotalItem();
        $this->productStock = null;
        $this->alert('success', 'Product Successfully Deleted');
    }

    public function createStockInProduct($StockInId)
    {
        $stockStatus = $this->isEdit ? StockStatus::CHANGE_ADD : StockStatus::ADD;
        foreach ($this->cart as $productStock) {
            $stock = ProductStock::where('id', $productStock['id'])->first();
            $stock->update([
                $this->stock_type => $stock[$this->stock_type] + $productStock['stock'],
                'all_stock' => $stock->all_stock + $productStock['stock']
            ]);
            setStockHistory(
                $stock->id,
                StockActivity::STOCK_IN,
                $stockStatus,
                NULL,
                $this->stock_type,
                $productStock['stock'],
                NULL,
                $stock->all_stock,
                $stock->home_stock,
                $stock->store_stock,
                $stock->pre_order_stock,
            );

            StockInProduct::create([
                'stock_in_id' => $StockInId,
                'product_stock_id' => $productStock['id'],
                'stock' => $productStock['stock'],
            ]);
        }
    }

    public function save()
    {
        $this->validate();
        try {
            $stockIn = StockIn::create([
                'user_id' => Auth::user()->id,
                'stock_type' => strtolower($this->stock_type),
                'total_items' => $this->total_items,
            ]);

            $this->createStockInProduct($stockIn->id);
            $this->reset();
            $this->alert('success', 'Stock In Succesfully Created');
            return redirect()->route('stock-in');
        } catch (\Throwable $th) {
            $this->alert('warning', $th->getMessage());
        }
    }

    public function edit()
    {
        $this->isEdit = true;
        $this->stock_type = strtolower($this->stockIn->stock_type);
        foreach ($this->stockIn->stockInProducts as $stockInProduct) {
            $this->cart[$stockInProduct->product_stock_id] = [
                'id' => $stockInProduct->product_stock_id,
                'color' => $stockInProduct->productStock->color->name,
                'size' => $stockInProduct->productStock->size->name,
                'product' => $stockInProduct->productStock->product->name,
                'stock' => $stockInProduct->stock,
            ];
        }
    }

    public function update()
    {
        $this->validate();
        foreach ($this->stockIn->stockInProducts as $stockInProduct) {
            $productStock = ProductStock::where('id', $stockInProduct->product_stock_id)->first();
            $productStock->update([
                $this->stockIn->stock_type->value => $productStock[$this->stockIn->stock_type->value] - $stockInProduct->stock,
            ]);
            setStockHistory(
                $productStock->id,
                StockActivity::STOCK_IN,
                StockStatus::CHANGE_REMOVE,
                $this->stockIn->stock_type->value,
                NULL,
                $stockInProduct->stock,
                NULL,
                $productStock->all_stock,
                $productStock->home_stock,
                $productStock->store_stock,
                $productStock->pre_order_stock,
            );
            $stockInProduct->delete();
        }
        $this->stockIn->update([
            'user_id' => Auth::user()->id,
            'stock_type' => $this->stock_type,
            'total_items' => $this->total_items,
        ]);
        $this->createStockInProduct($this->stockIn->id);
        $this->alert('success', 'Stock In Succesfully Updated');
        $this->mount($this->stockIn->id);
    }

    public function resetKeep() {
        $this->reset();
        $this->mount();
        $this->alert('success', 'Form Reset Successfully');
    }
}
