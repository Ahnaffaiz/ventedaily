<?php

namespace App\Livewire\Sale;

use App\Enums\DiscountType;
use App\Enums\KeepStatus;
use App\Enums\KeepType;
use App\Enums\PaymentType;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Group;
use App\Models\Keep;
use App\Models\Sale;
use App\Models\KeepProduct;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Setting;
use App\Models\TermOfPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateSale extends Component
{
    use LivewireAlert;

    public string $subtitle = 'Sale';
    public string $subRoute = 'sale';

    //existing data
    public $keeps, $keep, $keep_id;

    public $customers, $groups, $termOfPayments;
    public $sale, $isEdit;
    public $productStockList, $product_id, $productStock, $products;

    #[Rule('required')]
    public $group_id;

    #[Rule('required')]
    public $customer_id;

    #[Rule('required')]
    public $term_of_payment_id;

    public $desc;

    public $cart = [], $total_items, $total_price, $tax, $ship, $sub_total, $sub_total_after_discount;
    public $discount_type, $discount, $is_discount_program = 'yes', $discount_id, $discount_programs, $discount_program;

    public $isOpen = false, $modalType = 'product';

    #[Validate('required')]
    public $cash_received;
    public $cash_change, $bank_id, $account_number, $account_name, $code;

    #[Validate('required')]
    public $payment_type;

    #[Title('Create Sale')]
    #[Layout('layouts.app')]

    protected $listeners = [
        'deleteProductStock'
    ];

    public function mount($sale = null)
    {
        View::share('subtitle', $this->subtitle);
        View::share('subRoute', $this->subRoute);
        $this->termOfPayments = TermOfPayment::all()->pluck('name', 'id')->toArray();
        $this->products = Product::all()->pluck('name', 'id')->toArray();
        $this->discount_type = DiscountType::PERSEN;
        $this->discount_programs = Discount::all()->pluck('name', 'id')->toArray();
        $this->keeps = Keep::where('keep_time', '>=', Carbon::now())->where('status', strtolower(KeepStatus::ACTIVE))->pluck('no_keep', 'id')->toArray();
        $this->groups = Group::all()->pluck('name', 'id')->toArray();
        $this->customers = Customer::all()->pluck('name', 'id')->toArray();

        if($sale) {
            $this->sale = Sale::where('id', $sale)->first();
            $this->edit();
            $this->getTotalPrice();
        }
    }

    public function render()
    {
        return view('livewire.sale.create-sale')->with('subtitle', $this->subtitle);
    }

    public function openModal($modalType)
    {
        $this->product_id = null;
        $this->productStockList = null;
        $this->modalType = $modalType;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->getTotalPrice();
        $this->isOpen = false;
    }

    public function updatedIsDiscountProgram()
    {
        $this->discount_type = DiscountType::PERSEN;
        $this->discount = 0;
        $this->discount_id = null;
    }

    public function updatedGroupId()
    {
        $this->customers = Customer::where('group_id', $this->group_id)->pluck('name', 'id')->toArray();
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

    public function searchDiscount($query)
    {
        if ($query) {
            $this->discount_programs = collect(Discount::all()->pluck('name', 'id')->toArray())
                ->filter(function ($label, $value) use ($query) {
                    return stripos($label, $query) !== false;
                })
                ->toArray();
            }
    }

    public function searchKeep($query)
    {
        if ($query) {
            $this->keeps = collect(Keep::where('keep_time', '<=', Carbon::now())->pluck('no_keep', 'id')->toArray())
                ->filter(function ($label, $value) use ($query) {
                    return stripos($label, $query) !== false;
                })
                ->toArray();
            }
    }

    public function updatedProductId()
    {
        $this->productStockList = ProductStock::with('color', 'size')->where('product_id', $this->product_id)->get()->toArray();

        $this->productStockList = collect($this->productStockList)->map(function ($stockItem) {
            $cartItem = collect($this->cart)->firstWhere('id', $stockItem['id']);
            if ($cartItem) {
                $stockItem['home_stock'] += $cartItem['quantity'];
            }
            return $stockItem;
        })->toArray();
    }

    public function updatedDiscountId()
    {
        if($this->discount_id) {
            $this->discount_program = Discount::where('id', $this->discount_id)->first();
            $this->discount = $this->discount_program->value;
            $this->discount_type = $this->discount_program->discount_type;
        }
    }

    public function updatedKeepId()
    {
        if($this->keep_id) {
            $this->keep = Keep::where('id', $this->keep_id)->first();
            $this->group_id = $this->keep->customer->group_id;
            $this->customer_id = $this->keep->customer_id;
            $this->cart = [];
            foreach ($this->keep->keepProducts as $keepProduct) {
                $this->cart[$keepProduct->product_stock_id] = [
                    'id' => $keepProduct->product_stock_id,
                    'color' => $keepProduct->productStock->color->name,
                    'size' => $keepProduct->productStock->size->name,
                    'product' => $keepProduct->productStock->product->name,
                    'quantity' => $keepProduct->total_items,
                    'stock' => $keepProduct->productStock->home_stock + $keepProduct->total_items,
                    'selling_price' => $keepProduct->selling_price,
                    'total_price' => $keepProduct->total_price
                ];
            }
            $this->getTotalPrice();
        }
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

    public function addToCart($productStockId)
    {
        $productStock = ProductStock::where('id', $productStockId)->first();
        $this->cart[$productStockId]['selling_price'] = $productStock->selling_price;
        $this->cart[$productStockId] = [
            'id' => $productStock->id,
            'color' => $productStock->color->name,
            'size' => $productStock->size->name,
            'product' => $productStock->product->name,
            'quantity' => $this->cart[$productStockId]['quantity'],
            'stock' => $this->cart[$productStockId]['stock'],
            'selling_price' => $productStock->selling_price,
            'total_price' => $this->cart[$productStockId]['selling_price'] * $this->cart[$productStockId]['quantity']
        ];
        $this->getTotalPrice();
    }

    public function addProductStock($productStockId)
    {
        if (!isset($this->cart[$productStockId])) {
            $this->cart[$productStockId]['stock'] = ProductStock::where('id', $productStockId)->first()->home_stock;
            if ($this->cart[$productStockId]['stock'] > 0) {
                $this->cart[$productStockId]['quantity'] = 1;
                $this->addToCart($productStockId);
            } else {
                $this->alert('warning', "Out Of Stock");
            }
        } else {
            if($this->cart[$productStockId]['stock'] > $this->cart[$productStockId]['quantity']) {
                $this->cart[$productStockId]['quantity']++;
                $this->addToCart($productStockId);
            } else {
                $this->alert('warning', "Out Of Stock");
            }
        }
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
        $setting = Setting::first();
        try {
            $sale = Sale::create([
                'user_id' => Auth::user()->id,
                'keep_id' => $this->keep != null ? $this->keep->id : null,
                'no_sale' => $setting->sale_code . str_pad($setting->sale_increment + 1, 4, '0', STR_PAD_LEFT),
                'customer_id' => $this->customer_id,
                'term_of_payment_id' => $this->term_of_payment_id,
                'discount_type' => $this->discount_type ?? null,
                'discount' => $this->discount,
                'tax' => $this->tax,
                'ship' => $this->ship,
                'discount_id' => $this->discount_id ?? null,
                'sub_total' => $this->sub_total,
                'total_price' => $this->total_price,
                'total_items' => $this->total_items,
                'desc' => $this->desc,
            ]);

            if($this->keep != null) {
                $this->keep->update([
                    'status' => KeepStatus::SOLD
                ]);
            }

            $setting->update([
                'sale_increment' => $setting->sale_increment + 1
            ]);

            foreach ($this->cart as $productStock) {
                $saleProduct = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_stock_id' => $productStock['id'],
                    'total_items' => $productStock['quantity'],
                    'price' => $productStock['selling_price'],
                    'total_price' => $productStock['total_price']
                ]);
                if(!$this->keep) {
                    $stock = ProductStock::where('id', $productStock['id'])->first();
                    $stock->update([
                        'home_stock' => $stock->home_stock-$productStock['quantity'],
                        'all_stock' => $stock->all_stock-$productStock['quantity'],
                    ]);
                }
            }

            SalePayment::create([
                'sale_id' => $sale->id,
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
            $this->alert('success', 'Sale Succesfully Created');
            $this->mount();
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function edit()
    {
        $this->isEdit = true;
        $this->group_id = $this->sale->customer->group_id;
        $this->customers = Customer::where('group_id', $this->group_id)->pluck('name', 'id')->toArray();
        $this->customer_id = $this->sale->customer_id;
        $this->term_of_payment_id = $this->sale->term_of_payment_id;

        foreach ($this->sale->saleItems as $product) {
            $this->cart[$product->product_stock_id] = [
                'id' => $product->product_stock_id,
                'color' => $product->productStock->color->name,
                'size' => $product->productStock->size->name,
                'product' => $product->productStock->product->name,
                'quantity' => $product->total_items,
                'stock' => $product->productStock->home_stock,
                'selling_price' => $product->price,
                'total_price' => $product->total_price
            ];
        }

        $this->discount = $this->sale->discount;
        $this->discount_type = $this->sale->discount_type;
        $this->tax = $this->sale->tax;
        $this->ship = $this->sale->ship;
        $this->payment_type = $this->sale->salePayments->first()?->payment_type->key;
        $this->cash_received = $this->sale->salePayments->first()?->cash_received;
        $this->cash_change = $this->sale->salePayments->first()?->cash_change;
        if(strtolower($this->payment_type) === strtolower(PaymentType::TRANSFER)) {
            $this->bank_id = $this->sale->salePayments->first()?->bank_id;
            $this->account_number = $this->sale->salePayments->first()?->account_number;
            $this->account_name = $this->sale->salePayments->first()?->account_name;
        }
    }

    public function update()
    {
        $this->validate();
        $this->sale->update([
            'user_id' => Auth::user()->id,
            'keep_id' => $this->keep != null ? $this->keep->id : null,
            'customer_id' => $this->customer_id,
            'term_of_payment_id' => $this->term_of_payment_id,
            'discount_type' => $this->discount_type ?? null,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'ship' => $this->ship,
            'discount_id' => $this->discount_id ?? null,
            'sub_total' => $this->sub_total,
            'total_price' => $this->total_price,
            'total_items' => $this->total_items,
            'desc' => $this->desc,
        ]);

        foreach ($this->sale->saleItems as $saleItem) {
            $stock = ProductStock::where('id', $saleItem->product_stock_id)->first();
            $stock->update([
                'home_stock' => $stock->home_stock+$saleItem->total_items,
                'all_stock' => $stock->all_stock+$saleItem->total_items,
            ]);
            $saleItem->delete();
        }

        foreach ($this->cart as $productStock) {
            $saleProduct = SaleItem::create([
                'sale_id' => $this->sale->id,
                'product_stock_id' => $productStock['id'],
                'total_items' => $productStock['quantity'],
                'price' => $productStock['selling_price'],
                'total_price' => $productStock['total_price']
            ]);
            if(!$this->keep) {
                $stock = ProductStock::where('id', $productStock['id'])->first();
                $stock->update([
                    'home_stock' => $stock->home_stock-$productStock['quantity'],
                    'all_stock' => $stock->all_stock-$productStock['quantity'],
                ]);
            }
        }

        SalePayment::where('sale_id', $this->sale->id)->where('reference', 'First Payment')->update([
            'sale_id' => $this->sale->id,
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

        $this->alert('success', 'Sale Succesfully Updated');
        $this->mount();


    }
}
