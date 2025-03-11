<?php

namespace App\Livewire\Product;

use App\Models\Color;
use App\Models\Product;
use App\Models\ProductStock as ModelsProductStock;
use App\Models\Size;
use Exception;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ProductStock extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    public Product $product;
    public $sizes, $colors;
    public $size_id, $color_id, $selling_price, $purchase_price, $margin_price, $status;
    public $query = '', $sortBy = 'size_id', $sortDirection = 'asc';
    public $showColumns = [
        'size_id' => true,
        'color_id' => true,
        'status' => true,
        'selling_price' => true,
        'purchase_price' => true,
        'all_stock' => true,
        'home_stock' => true,
        'store_stock' => true,
        'qc_stock' => false,
        'vermak_stock' => false,
        'created_at' => false,
        'updated_at' => false,
    ];
    public $productStock = null;

    protected $rules = [
        'size_id' => 'required',
        'color_id' => 'required',
        'selling_price' => 'required|numeric',
        'purchase_price' => 'required|numeric',
        'status' => 'required',

    ];

    protected $listeners = [
        'delete'
    ];

    public function updatedQuery()
    {
        $this->resetPage();
    }

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

    public function mount($product) {
        $this->product = Product::where('id',$product->id)->first();
        $this->sizes = Size::all()->pluck('name', 'id')->toArray();
        $this->colors = Color::all()->pluck('name', 'id')->toArray();
    }
    public function render()
    {
        return view('livewire.product.product-stock', [
            'productStocks' => ModelsProductStock::orderBy($this->sortBy, $this->sortDirection)
                ->where('product_id', $this->product->id)
                ->paginate(5)
        ]);
    }

    public function updatedPurchasePrice()
    {
        $this->margin_price = (int) $this->selling_price - (int) $this->purchase_price;
    }

    public function updatedSellingPrice()
    {
        $this->margin_price = (int) $this->selling_price - (int) $this->purchase_price;
    }

    public function save()
    {
        $this->validate($this->rules);
        if($this->productStock) {
            $this->productStock->update([
                'size_id' => $this->size_id,
                'color_id' => $this->color_id,
                'selling_price' => $this->selling_price,
                'purchase_price' => $this->purchase_price,
                'status' => $this->status,
            ]);
            $this->alert('success', 'Product Successfully Updated');
        } else {
            try {
                ModelsProductStock::firstOrCreate([
                    'product_id' => $this->product->id,
                    'color_id' => $this->color_id,
                    'size_id' => $this->size_id,
                ],[
                    'selling_price' => $this->selling_price,
                    'purchase_price' => $this->purchase_price,
                    'all_stock' => 0,
                    'home_stock' => 0,
                    'qc_stock' => 0,
                    'store_stock' => 0,
                    'vermak_stock' => 0,
                ]);
                $this->alert('success', 'Product Successfully Created');
            } catch (Exception $th) {
                dd($th);
                $this->alert('error', 'Can\'t Create Product', ['text'=>$th]);
            }
        }
        $this->formReset();
    }

    public function edit($id)
    {
        $this->productStock = ModelsProductStock::find($id);
        $this->size_id = $this->productStock->size_id;
        $this->color_id = $this->productStock->color_id;
        $this->status = $this->productStock->status;
        $this->purchase_price = $this->productStock->purchase_price;
        $this->selling_price = $this->productStock->selling_price;
        $this->margin_price = $this->productStock->selling_price - $this->productStock->purchase_price;
    }

    public function deleteAlert($product)
    {
        $this->productStock = ModelsProductStock::find($product);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete this product ?',
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Yes',
            'showCancelButton' => true,
            'cancelButtonText' => 'cancel',
            'icon' => 'warning',
            'onConfirmed' => 'delete',
            'timer' => null,
            'confirmButtonColor' => '#3085d6',
            'cancelButtonColor' => '#d33'
        ]);
    }

    public function delete()
    {
        try {
            $this->productStock->delete();
            $this->alert('success', 'Product Succesfully Deleted');
        } catch (\Throwable $th) {
            $this->alert('error', 'Can\'t Delete Product');
        }
    }

    public function cancel()
    {
        $this->reset();
    }

    public function formReset()
    {
        $this->size_id = null;
        $this->color_id = null;
        $this->selling_price = null;
        $this->purchase_price = null;
        $this->margin_price = null;
        $this->status = null;
    }
}
