<?php

namespace App\Livewire\Sale;

use App\Enums\DiscountType;
use App\Enums\PaymentType;
use App\Models\SalePayment as ModalSalePayment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SalePayment extends Component
{
    use LivewireAlert;

    public $sale, $payment;

    #[Validate('required')]
    public $payment_type;

    #[Validate('required')]
    public $cash_received;

    #[Validate('required')]
    public $cash_change, $total_price, $out_balance, $sub_total_after_discount;

    #[Validate('required')]
    public $reference;
    public $bank_id, $account_number, $account_name, $desc;

    protected $listeners = [
        'delete'
    ];

    public function mount($sale) {
        $this->sale = $sale;
        $this->total_price = $this->sale?->total_price;
        $this->out_balance = $this->sale?->outstanding_balance;
        $this->getSubTotalAfterDiscount();
    }
    public function render()
    {
        return view('livewire.sale.sale-payment', [
            'payments' => ModalSalePayment::where('sale_id', $this->sale->id)->orderBy('created_at')->get()
        ]);
    }

    public function getSubTotalAfterDiscount()
    {
        if($this->sale?->discount_type === DiscountType::PERSEN) {
            $this->sub_total_after_discount = $this->sale?->sub_total - round($this->sale?->sub_total* (int) $this->sale?->discount/100);
        } elseif($this->sale?->discount_type === DiscountType::RUPIAH) {
            $this->sub_total_after_discount = $this->sale?->sub_total - $this->sale?->discount;
        } else {
            $this->sub_total_after_discount = $this->sale?->sub_total;
        }
    }

    public function updatedCashReceived()
    {
        $payment = ModalSalePayment::where('sale_id', $this->sale->id)->count();
        if($payment > 1) {
            $this->cash_change = ($this->sale->salePayments->sum('amount') + (int) $this->cash_received) - $this->total_price;
            if($this->payment) {
                $this->cash_change = $this->cash_change - $this->payment->amount;
            }
        } else {
            $this->cash_change = $this->cash_received - $this->total_price;
        }
    }

    public function edit($payment)
    {
        $this->payment = ModalSalePayment::find($payment);
        $this->cash_received = $this->payment?->cash_received;
        $this->cash_change = $this->payment?->cash_change;
        $this->payment_type = strtolower($this->payment?->payment_type);
        $this->account_name = $this->payment?->account_name;
        $this->account_number = $this->payment?->account_number;
        $this->bank_id = $this->payment?->bank_id;
        $this->desc = $this->payment?->desc;
        $this->reference = $this->payment?->reference;
    }

    public function save()
    {
        $this->validate();
        try {
            if($this->payment) {
                $this->payment->update([
                    'reference' => $this->reference,
                    'date' => Carbon::now(),
                    'amount' => $this->cash_change > 0 ? $this->total_price : $this->cash_received,
                    'cash_received' => $this->cash_received,
                    'cash_change' => $this->cash_change,
                    'payment_type' => strtolower($this->payment_type),
                    'account_number' => $this->account_number,
                    'account_name' => $this->account_name,
                    'desc' => $this->desc,
                    'bank_id' => $this->bank_id
                ]);
                $this->payment = null;
                $this->alert('success', 'Payment Successfully Updated');
            } else {
                ModalSalePayment::create([
                    'sale_id' => $this->sale?->id,
                    'user_id' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'reference' => $this->reference,
                    'amount' => $this->cash_change > 0 ? -1 * $this->out_balance : $this->cash_received,
                    'cash_received' => $this->cash_received,
                    'cash_change' => $this->cash_change,
                    'payment_type' => strtolower($this->payment_type),
                    'account_number' => $this->account_number,
                    'account_name' => $this->account_name,
                    'desc' => $this->desc,
                    'bank_id' => $this->bank_id
                ]);
                $this->alert('success', 'Payment Successfully Added');
            }

            //update outstanding balance
            $this->out_balance = $this->total_price > $this->sale->salePayments->sum('amount') ? -1 * ($this->total_price - $this->sale->salePayments->sum('amount')) : 0;
            $this->sale->update([
                'outstanding_balance' => $this->out_balance
            ]);
            $this->resetInput();
        } catch (\Exception $exception) {
            $this->alert('warning', 'Error' . $exception);
        }
    }

    public function deleteAlert($payment)
    {
        $this->payment = ModalSalePayment::find($payment);
        $this->alert('question', 'Delete', [
            'toast' => false,
            'text' => 'Are you sure to delete this payment ?',
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
        $this->sale->update([
            'outstanding_balance' => $this->sale->outstanding_balance - $this->payment->amount
        ]);
        $this->payment->delete();
        $this->payment = null;
        $this->mount($this->sale);
    }

    public function resetInput()
    {
        $this->reference = null;
        $this->bank_id = null;
        $this->cash_received = null;
        $this->cash_change = null;
        $this->account_number = null;
        $this->account_name = null;
        $this->payment_type = null;
        $this->desc = null;
    }

    public function printPayment($payment)
    {
        $payment = ModalSalePayment::with('sale', 'sale.customer', 'bank')->where('id', $payment)->first();
        $setting = Setting::first();
        Session::put('sale-payment', $payment);
        Session::put('setting', $setting);
        $this->dispatch('print-sale-payment',route('print-sale-payment', ['payment' => $payment->id]));
    }
}
