<?php

namespace App\Livewire\Purchase;

use App\Enums\PaymentType;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PurchasePayment extends Component
{

    public $purchase, $payment;

    #[Validate('required')]
    public $payment_type;

    #[Validate('required')]
    public $cash_received;

    #[Validate('required')]
    public $cash_change;

    public $bank_id, $account_number, $account_name, $desc;

    public function mount($purchase) {
        $this->purchase = $purchase;
    }
    public function render()
    {
        return view('livewire.purchase.purchase-payment');
    }

    public function updatedCashReceived()
    {
        $this->cash_change = (int) $this->cash_received - (int) $this->total_price;
    }

    public function save()
    {
        $this->validate($this->rules);
        if($this->payment) {
            if($this->payment_type != PaymentType::TRANSFER) {
                $this->payment_type
            }
        }
    }
}
