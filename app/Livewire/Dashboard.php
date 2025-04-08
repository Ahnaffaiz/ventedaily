<?php

namespace App\Livewire;

use App\Models\Keep;
use Livewire\Attributes\Title;
use Livewire\Component;

class Dashboard extends Component
{
    public $keep_items, $keep_prices;

    public $sales, $profit, $purchase, $costs;
    #[Title('Dashboard')]
    public function render()
    {
        $this->keep_items = Keep::allTotalItems();
        $this->keep_prices = Keep::allTotalPrice();
        return view('livewire.dashboard');
    }
}
