<?php

namespace App\Livewire\Purchase;

use App\Enums\DiscountType;
use App\Enums\PaymentType;
use App\Models\ProductStock;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use App\Models\TermOfPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreatePurchase extends Component
{
    use LivewireAlert;

    public $purchase, $isEdit;
    public $suppliers, $termOfPayments;
    public string $subtitle = 'Purchase';
    public string $subRoute = 'purchase';
    public $productStockList;
    public $product_id;
    public $productStock;

    public $cart = [], $total_items, $total_price, $discount_type, $discount, $tax, $sub_total, $sub_total_after_discount;

    #[Validate('required')]
    public $supplier_id;

    #[Validate('required')]
    public $term_of_payment_id;
    #[Validate('required')]
    public $cash_received;
    public $cash_change, $desc, $bank_id, $account_number, $account_name;

    #[Validate('required')]
    public $payment_type;

    public $isOpen = false, $modalType = 'product';

    #[Title('Create Purchase')]
    #[Layout('layouts.app')]

    protected $listeners = [
        'deleteProductStock'
    ];

    public function mount($purchase = null)
    {
        View::share('subtitle', $this->subtitle);
        View::share('subRoute', $this->subRoute);
        $this->suppliers = Supplier::all()->pluck('name', 'id')->toArray();
        $this->termOfPayments = TermOfPayment::all()->pluck('name', 'id')->toArray();

        if($purchase) {
            $this->purchase = Purchase::where('id', $purchase)->first();
            $this->edit();
            $this->getTotalPrice();
        } else {
            $this->suppliers = Supplier::all()->pluck('name', 'id')->toArray();
            $this->termOfPayments = TermOfPayment::all()->pluck('name', 'id')->toArray();

            //session
            $this->cart = Session::get('cart', []);
            $this->discount = Session::get('discount');
            $this->discount_type = Session::get('discount_type', DiscountType::PERSEN);
            $this->tax = Session::get('tax');
            $this->getTotalPrice();
        }

    }

    public function render()
    {
        return view('livewire.purchase.create-purchase')->with('subtitle', $this->subtitle);;
    }

    public function openModal($modalType)
    {
        $this->modalType = $modalType;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function saveDiscountTax()
    {
        if($this->modalType == 'discount') {
            Session::put('discount', $this->discount);
            Session::put('discount_type', $this->discount_type);
            $this->alert('success', 'Discount Successfully Added');
        } elseif($this->modalType == 'tax') {
            Session::put('tax', $this->tax);
            $this->alert('success', 'Tax Successfully Added');
        }

        $this->getTotalPrice();
        $this->modalType = 'product';
        $this->isOpen = false;
    }

    public function getTotalPrice()
    {
        $this->total_items = array_sum(array_column($this->cart, 'quantity'));
        $this->sub_total = array_sum(array_column($this->cart, 'total_price'));
        $this->total_price = $this->sub_total;
        if($this->discount_type === DiscountType::PERSEN) {
            $this->sub_total_after_discount = $this->sub_total - round($this->sub_total* (int) $this->discount/100);
            $this->total_price = $this->sub_total_after_discount;
        } elseif($this->discount_type === DiscountType::RUPIAH) {
            $this->sub_total_after_discount = $this->sub_total - $this->discount;
            $this->total_price = $this->sub_total_after_discount;
        }
        if($this->tax) {
            $this->total_price = $this->sub_total_after_discount + round($this->sub_total_after_discount* (int) $this->tax/100);
        }

        if($this->cash_received) {
            $this->cash_change = (int) $this->cash_received - (int) $this->total_price;
        }

        Session::put('sub_total', $this->sub_total);
        Session::put('sub_total_after_discount', $this->sub_total_after_discount);
        Session::put('total_price', $this->total_price);
    }

    public function updatedProductId()
    {
        $this->productStockList = ProductStock::where('product_id', $this->product_id)->get();
    }

    public function addToCart($productStockId)
    {
        $productStock = ProductStock::where('id', $productStockId)->first();
        $this->cart[$productStockId]['purchase_price'] = $productStock->purchase_price;
        $cart = Session::get('cart', []);
        if (isset($cart[$productStockId])) {
            $cart[$productStockId]['quantity'] = $this->cart[$productStockId]['quantity'];
            $cart[$productStockId]['total_price'] = $this->cart[$productStockId]['quantity'] * $this->cart[$productStockId]['purchase_price'];
        } else {
            $cart[$productStockId] = [
                'id' => $productStock->id,
                'color' => $productStock->color->name,
                'size' => $productStock->size->name,
                'price' => $productStock->purchase_price,
                'product' => $productStock->product->name,
                'quantity' => $this->cart[$productStockId]['quantity'],
                'purchase_price' => $productStock->purchase_price,
                'total_price' => $this->cart[$productStockId]['purchase_price'] * $this->cart[$productStockId]['quantity']
            ];
        }
        $this->cart = $cart;
        $this->getTotalPrice();
        Session::put('cart', $cart);
    }

    public function addProductStock($productStockId)
    {
        if (!isset($this->cart[$productStockId])) {
            $this->cart[$productStockId]['quantity'] = 1;
        } else {
            $this->cart[$productStockId]['quantity']++;
        }
        $this->addToCart($productStockId);
    }

    public function removeProductStock($productStockId)
    {
        if (isset($this->cart[$productStockId]) && $this->cart[$productStockId]['quantity'] > 1) {
            $this->cart[$productStockId]['quantity']--;
            $this->addToCart($productStockId);
        } else {
            $this->productStock = $this->cart[$productStockId]['id'];
            $this->alert('question', 'Delete', [
                'toast' => false,
                'text' => 'Are you sure to remove ' . $this->cart[$productStockId]['product'] .' ?',
                'position' => 'center',
                'showConfirmButton' => true,
                'confirmButtonText' => 'Yes',
                'showCancelButton' => true,
                'cancelButtonText' => 'cancel',
                'icon' => 'warning',
                'onConfirmed' => 'deleteProductStock',
                'timer' => null,
                'confirmButtonColor' => '#3085d6',
                'cancelButtonColor' => '#d33'
            ]);
        }
    }

    public function deleteProductStock()
    {
        unset($this->cart[$this->productStock]);
        Session::put('cart', $this->cart);
        $this->getTotalPrice();
        $this->alert('success', 'Product Successfully Deleted');
    }

    public function updatedCashReceived()
    {
        $this->cash_change = (int) $this->cash_received - (int) $this->total_price;
    }

    public function save()
    {
        $this->validate();
        $purchase = Purchase::create([
            'user_id' => Auth::user()->id,
            'supplier_id' => $this->supplier_id,
            'term_of_payment_id' => $this->term_of_payment_id,
            'discount_type' => $this->discount_type ?? null,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'sub_total' => $this->sub_total,
            'total_price' => $this->total_price,
            'total_items' => $this->total_items,
            'desc' => $this->desc,
            'outstanding_balance' => $this->cash_change < 0 ? $this->cash_change : 0
        ]);

        foreach ($this->cart as $productStock) {
            $purchaseItem = PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_stock_id' => $productStock['id'],
                'total_items' => $productStock['quantity'],
                'price' => $productStock['purchase_price'],
                'total_price' => $productStock['total_price']
            ]);
            $stock = ProductStock::where('id', $productStock['id'])->first();
            $stock->update([
                'home_stock' => $stock->home_stock+$productStock['quantity'],
                'all_stock' => $stock->all_stock+$productStock['quantity'],
            ]);
        }

        PurchasePayment::create([
            'purchase_id' => $purchase->id,
            'user_id' => Auth::user()->id,
            'date' => Carbon::now(),
            'reference' => 'First Payment',
            'amount' => $this->cash_received,
            'cash_received' => $this->cash_received,
            'cash_change' => $this->cash_change,
            'payment_type' => strtolower($this->payment_type),
            'account_number' => $this->account_number,
            'account_name' => $this->account_name,
            'desc' => $this->desc,
            'bank_id' => $this->bank_id
        ]);

        Session::remove('cart');
        Session::remove('discount');
        Session::remove('tax');
        Session::remove('discount_type');
        $this->reset();
        $this->alert('success', 'Purchase Order Succesfully Created');
        $this->mount();
    }

    public function edit()
    {
        $this->isEdit = true;
        $this->supplier_id = $this->purchase->supplier_id;
        $this->term_of_payment_id = $this->purchase->term_of_payment_id;

        foreach ($this->purchase->purchaseItems as $purchaseitem) {
            $this->cart[$purchaseitem->product_stock_id] = [
                'id' => $purchaseitem->product_stock_id,
                'color' => $purchaseitem->productStock->color->name,
                'size' => $purchaseitem->productStock->size->name,
                'price' => $purchaseitem->productStock->purchase_price,
                'product' => $purchaseitem->productStock->product->name,
                'quantity' => $purchaseitem->total_items,
                'purchase_price' => $purchaseitem->price,
                'total_price' => $purchaseitem->total_price
            ];
        }
        $this->discount = $this->purchase->discount;
        $this->discount_type = $this->purchase->discount_type;
        $this->tax = $this->purchase->tax;
        $this->payment_type = $this->purchase->purchasePayments->first()?->payment_type->value;
        $this->cash_received = $this->purchase->purchasePayments->first()?->cash_received;
        $this->cash_change = $this->purchase->purchasePayments->first()?->cash_change;
        if($this->payment_type === PaymentType::TRANSFER) {
            $this->bank_id = $this->purchase->purchasePayments->first()?->bank_id;
            $this->account_number = $this->purchase->purchasePayments->first()?->account_number;
            $this->account_name = $this->purchase->purchasePayments->first()?->account_name;
        }

        Session::put('cart', $this->cart);
        Session::put('discount', $this->discount);
        Session::put('discount_type', $this->discount_type);
        Session::put('tax', $this->tax);
    }
    public function update()
    {
        $this->validate();
        $this->purchase->update([
            'user_id' => Auth::user()->id,
            'supplier_id' => $this->supplier_id,
            'term_of_payment_id' => $this->term_of_payment_id,
            'discount_type' => $this->discount_type ?? null,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'sub_total' => $this->sub_total,
            'total_price' => $this->total_price,
            'total_items' => $this->total_items,
            'desc' => $this->desc,
            'outstanding_balance' => $this->cash_change < 0 ? $this->cash_change : 0
        ]);

        foreach ($this->purchase->purchaseItems as $purchaseItem) {
            $stock = ProductStock::where('id', $purchaseItem->product_stock_id)->first();
            $stock->update([
                'home_stock' => $stock->home_stock-$purchaseItem->total_items,
                'all_stock' => $stock->all_stock-$purchaseItem->total_items,
            ]);
            $purchaseItem->delete();
        }

        foreach ($this->cart as $productStock) {
            $purchaseItem = PurchaseItem::create([
                'purchase_id' => $this->purchase->id,
                'product_stock_id' => $productStock['id'],
                'total_items' => $productStock['quantity'],
                'price' => $productStock['purchase_price'],
                'total_price' => $productStock['total_price']
            ]);
            $stock = ProductStock::where('id', $productStock['id'])->first();
            $stock->update([
                'home_stock' => $stock->home_stock+$productStock['quantity'],
                'all_stock' => $stock->all_stock+$productStock['quantity'],
            ]);
        }

        PurchasePayment::where('purchase_id', $this->purchase->id)->where('reference', 'First Payment')->update([
            'purchase_id' => $this->purchase->id,
            'user_id' => Auth::user()->id,
            'date' => Carbon::now(),
            'reference' => 'First Payment',
            'amount' => $this->cash_received,
            'cash_received' => $this->cash_received,
            'cash_change' => $this->cash_change,
            'payment_type' => strtolower($this->payment_type),
            'account_number' => $this->account_number,
            'account_name' => $this->account_name,
            'desc' => $this->desc,
            'bank_id' => $this->bank_id
        ]);

        $this->alert('success', 'Purchase Order Succesfully Updated');
        $this->mount();
    }

}
