<?php

namespace App\Livewire\Product;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;

class CreateProduct extends Component
{
    use LivewireAlert;

    public $categories, $product, $productStocks;
    public $name, $category_id, $is_favorite=false, $imei, $status;

    #[Title('Create')]
    public function mount()
    {
        $this->categories = Category::all()->pluck('name', 'id')->toArray();
    }
    public function render()
    {
        if($this->product) {
            $this->productStocks = ProductStock::where('product_id', $this->product->id)->get();
        }
        return view('livewire.product.create-product');
    }

    public function save() {
        if($this->product) {
            $this->product->update([
                'name' => $this->name,
                'category_id' => $this->category_id,
                'is_favorite' => $this->is_favorite,
                'imei' => $this->imei,
                'code' => '876876878',
                'status' => $this->status,
                'updated_at' => Carbon::now()
            ]);
            $this->alert('success', 'Product Successfully Updated');
        } else {

        }

    }
}