<?php

namespace App\Livewire\Product;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product as ModelsProduct;
use Carbon\Carbon;
use Exception;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Product extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    public $isOpen = false;
    public $categories, $product, $isProductStock = false;
    public $name, $desc, $category_id, $is_favorite=false, $imei, $status;
    public $query = '', $perPage = 10, $sortBy = 'name', $sortDirection = 'asc';
    public $showColumns = [
        'category_id' => true,
        'imei' => true,
        'status' => true,
        'code' => false,
        'is_favorite' => true,
        'created_at' => false,
        'updated_at' => false,
    ];

    #[Title('Product')]

    protected $rules = [
        'name' => 'required|unique:products',
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

    public function mount()
    {
        $this->categories = Category::all()->pluck('name', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.product.product', [
            'products' =>ModelsProduct::orderBy($this->sortBy, $this->sortDirection)
                    ->where('name', 'like', '%'.$this->query.'%')
                    ->paginate($this->perPage)
        ]);
    }

    public function openModal()
    {
        $this->reset();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset();
    }

    public function save()
    {
        $this->validate($this->rules);
        ModelsProduct::firstOrCreate(['name' => $this->name],[
            'category_id' => $this->category_id,
            'is_favorite' => $this->is_favorite,
            'imei' => $this->imei,
            'code' => '876876878',
            'status' => $this->status,
            'desc' => $this->desc
        ]);
        $this->alert('success', 'Product Successfully Created');
        try {
            $this->closeModal();
        } catch (Exception $th) {
            $this->alert('error', 'Can\'t Create Product', [
                'text' => $th
            ]);
        }
    }

    public function edit($product)
    {
        $this->product = ModelsProduct::find($product);
        $this->name = $this->product->name;
        $this->desc = $this->product->desc;
        $this->category_id = $this->product->category_id;
        $this->category_id = $this->product->category_id;
        $this->is_favorite = (bool) $this->product->is_favorite;
        $this->imei = $this->product->imei;
        $this->status = $this->product->status->value;
        $this->isOpen = true;
    }

    public function update() {
        if(!$this->isProductStock) {
            $this->product->update([
                'name' => $this->name,
                'desc' => $this->desc,
                'category_id' => $this->category_id,
                'is_favorite' => $this->is_favorite,
                'imei' => $this->imei,
                'status' => $this->status,
                'updated_at' => Carbon::now()
            ]);
            $this->closeModal();
            $this->alert('success', 'Product Successfully Updated');
        } else {
            $this->closeModal();
            $this->alert('success', 'Product Successfully Updated');
        }

    }

    public function deleteAlert($product)
    {
        $this->product = ModelsProduct::find($product);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete ' . $this->product->name .' ?',
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
        if ($this->product->productStocks->count() > 0) {
            $this->alert('warning', 'Can\'t Delete Product');
        } else {
            $this->product->delete();
            $this->alert('success', 'Product Succesfully Deleted');
        }
    }

    public function cancel()
    {
        $this->reset();
    }

    public function addProductStock($product)
    {
        $this->product = ModelsProduct::find($product);
        $this->isProductStock = true;
        $this->isOpen = true;
    }
}
