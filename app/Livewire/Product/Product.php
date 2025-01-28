<?php

namespace App\Livewire\Product;

use Livewire\Attributes\Title;
use Livewire\Component;

class Product extends Component
{
    #[Title('Product List')]
    public function render()
    {
        return view('livewire.product.product');
    }
}