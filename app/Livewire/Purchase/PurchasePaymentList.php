<?php

namespace App\Livewire\Purchase;

use App\Models\PurchasePayment;
use Livewire\Component;

class PurchasePaymentList extends Component
{
    public $purchase;

    public function mount($purchase)
    {
        $this->purchase = $purchase;
    }
    public function render()
    {
        return view('livewire.purchase.purchase-payment-list', [
            'payments' => PurchasePayment::where('purchase_id', $this->purchase->id)->orderBy('created_at')->get()
        ]);
    }
}
