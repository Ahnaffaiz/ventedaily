<?php

namespace App\Livewire\Product;

use App\Imports\ProductStockImport;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ImportProduct extends Component
{
    use WithFileUploads;

    #[Rule("required")]
    public $product_file;

    #[Title("Import Product")]
    public function render()
    {
        return view('livewire.product.import-product');
    }

    public function importProduct()
    {
        Excel::import(new ProductStockImport, $this->product_file);
    }
}
