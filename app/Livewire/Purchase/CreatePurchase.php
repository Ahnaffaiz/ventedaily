<?php

namespace App\Livewire\Purchase;

use App\Enums\DiscountType;
use App\Enums\PaymentType;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use App\Models\TermOfPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
    public $productStockList, $product_id, $productStock;

    public $cart = [], $total_items, $total_price, $discount_type, $discount, $tax, $ship, $sub_total, $sub_total_after_discount;

    #[Validate('required')]
    public $supplier_id;

    #[Validate('required')]
    public $term_of_payment_id;
    #[Validate('required')]
    public $cash_received;
    public $cash_change, $desc, $bank_id, $account_number, $account_name, $products;

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
        $this->products = Product::all()->pluck('name', 'id')->toArray();
        $this->suppliers = Supplier::all()->pluck('name', 'id')->toArray();
        $this->termOfPayments = TermOfPayment::all()->pluck('name', 'id')->toArray();
        $this->discount_type = DiscountType::PERSEN;

        if($purchase) {
            $this->purchase = Purchase::where('id', $purchase)->first();
            $this->edit();
            $this->getTotalPrice();
        }

    }

    public function render()
    {
        return view('livewire.purchase.create-purchase')->with('subtitle', $this->subtitle);
    }

    public function openModal($modalType)
    {
        $this->modalType = $modalType;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->getTotalPrice();
        $this->isOpen = false;
    }

    public function searchProduct($query)
    {
        $this->products = Product::all()->pluck('name', 'id')->toArray();
        if ($query) {
            $this->products = collect(Product::all()->pluck('name', 'id')->toArray())
                ->filter(function ($label, $value) use ($query) {
                    return stripos($label, $query) !== false;
                })
                ->toArray();
            }
    }

    public function getTotalPrice()
    {
        $this->total_items = array_sum(array_column($this->cart, 'quantity'));
        $this->sub_total = array_sum(array_column($this->cart, 'total_price'));
        $this->total_price = $this->sub_total;
        if(strtolower($this->discount_type) === strtolower(DiscountType::PERSEN) ) {
            $this->sub_total_after_discount = $this->sub_total - round($this->sub_total* (int) $this->discount/100);
            $this->total_price = $this->sub_total_after_discount;
        } elseif(strtolower($this->discount_type) === strtolower(DiscountType::RUPIAH)) {
            $this->sub_total_after_discount = $this->sub_total - $this->discount;
            $this->total_price = $this->sub_total_after_discount;
        } else {
            $this->sub_total_after_discount = $this->total_price;
        }
        if($this->tax) {
            $this->total_price = $this->sub_total_after_discount + round($this->sub_total_after_discount* (int) $this->tax/100);
        }
        if($this->ship) {
            $this->total_price = $this->total_price + $this->ship;
        }

        if($this->cash_received) {
            $this->cash_change = (int) $this->cash_received - (int) $this->total_price;
        }
    }

    public function updatedProductId()
    {
        $this->productStockList = ProductStock::where('product_id', $this->product_id)->get();
    }

    public function addToCart($productStockId)
    {
        $productStock = ProductStock::where('id', $productStockId)->first();
        if($this->cart[$productStockId]['quantity'] < 1) {
            $this->cart[$productStockId]['quantity'] = 1;
        }
        $this->cart[$productStockId] = [
            'id' => $productStock->id,
            'color' => $productStock->color->name,
            'size' => $productStock->size->name,
            'price' => $productStock->purchase_price,
            'product' => $productStock->product->name,
            'quantity' => $this->cart[$productStockId]['quantity'],
            'purchase_price' => $productStock->purchase_price,
            'total_price' => $productStock->purchase_price * $this->cart[$productStockId]['quantity']
        ];
        $this->getTotalPrice();
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
        } elseif(isset($this->cart[$productStockId]) && $this->cart[$productStockId]['quantity'] == 1) {
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
        } else {
            $this->alert('warning','Product not added');
        }
    }

    public function deleteProductStock()
    {
        unset($this->cart[$this->productStock]);
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
            'ship' => $this->ship,
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
            'amount' => $this->cash_change > 0 ? $this->total_price : $this->cash_received,
            'cash_received' => $this->cash_received,
            'cash_change' => $this->cash_change,
            'payment_type' => strtolower($this->payment_type),
            'account_number' => $this->account_number,
            'account_name' => $this->account_name,
            'desc' => $this->desc,
            'bank_id' => $this->bank_id
        ]);

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
        $this->ship = $this->purchase->ship;
        $this->payment_type = $this->purchase->purchasePayments->first()?->payment_type->key;
        $this->cash_received = $this->purchase->purchasePayments->first()?->cash_received;
        $this->cash_change = $this->purchase->purchasePayments->first()?->cash_change;
    }
    public function update()
    {
        $this->payment_type = PaymentType::CASH;
        $this->cash_received = 0;
        $this->validate();
        $this->purchase->update([
            'user_id' => Auth::user()->id,
            'supplier_id' => $this->supplier_id,
            'term_of_payment_id' => $this->term_of_payment_id,
            'discount_type' => $this->discount_type ?? null,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'ship' => $this->ship,
            'sub_total' => $this->sub_total,
            'total_price' => $this->total_price,
            'total_items' => $this->total_items,
            'desc' => $this->desc,
            'outstanding_balance' => -1 * $this->total_price
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

        foreach ($this->purchase->purchasePayments as $purchasePayments) {
            $purchasePayments->delete();
        }

        $this->alert('success', 'Purchase Order Succesfully Updated');
        $this->mount();
    }

    public function resetPurchase()
    {
        $this->purchase = null;
        $this->cart = null;
        $this->supplier_id = null;
        $this->term_of_payment_id = null;
        $this->payment_type = null;
        $this->cash_received = null;
        $this->cash_change = null;
        $this->cash_change = null;
        $this->bank_id = null;
        $this->account_number = null;
        $this->account_name = null;
    }

}
