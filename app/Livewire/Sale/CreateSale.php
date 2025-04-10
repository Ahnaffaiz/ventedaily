<?php

namespace App\Livewire\Sale;

use App\Enums\DiscountType;
use App\Enums\KeepStatus;
use App\Enums\KeepType;
use App\Enums\PaymentType;
use App\Enums\StockActivity;
use App\Enums\StockStatus;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Group;
use App\Models\Marketplace;
use App\Models\Keep;
use App\Models\PreOrder;
use App\Models\PreOrderProduct;
use App\Models\ProductStockHistory;
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
    public $preOrders, $preOrder, $pre_order_id;
    public $saleFrom = 'keep';

    public $no_sale;

    public $customers, $groups, $termOfPayments, $marketplaces;
    public $marketplace_id, $order_id_marketplace;
    public $sale, $isEdit;
    public $productStockList, $product_id, $productStock, $products;

    #[Rule('required')]
    public $group_id, $customer_id, $term_of_payment_id;


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
        $this->term_of_payment_id = TermOfPayment::where('name', 'cash')->first()->id;
        $this->payment_type = strtolower(PaymentType::CASH);
        $this->termOfPayments = TermOfPayment::all()->pluck('name', 'id')->toArray();
        $this->products = Product::all()->pluck('name', 'id')->toArray();
        $this->marketplaces = Marketplace::all()->pluck('name', 'id')->toArray();
        $this->discount_type = DiscountType::PERSEN;
        $this->discount_programs = Discount::all()->pluck('name', 'id')->toArray();
        $this->keeps = Keep::where('keep_time', '>=', Carbon::now())->where('status', strtolower(KeepStatus::ACTIVE))->pluck('no_keep', 'id')->toArray();
        $this->preOrders = PreOrder::whereNotIn('id', Sale::where('pre_order_id', '!=', null)->pluck('pre_order_id'))->pluck('no_pre_order', 'id')->toArray();
        $this->groups = Group::all()->pluck('name', 'id')->toArray();
        $this->customers = Customer::all()->pluck('name', 'id')->toArray();

        if($sale) {
            $this->sale = Sale::where('id', $sale)->first();
            $this->no_sale = $this->sale->no_sale;
            $this->edit();
            $this->getTotalPrice();
        } else {
            $setting = Setting::first();
            $this->no_sale = $setting->sale_code . str_pad($setting->sale_increment + 1, 4, '0', STR_PAD_LEFT);
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
        if($this->customer_id == null || $this->group_id == null) {
            $this->alert('warning', 'select Group dan Customer first');
        } else {
            $this->isOpen = true;
        }
    }

    public function closeModal()
    {
        if($this->cart != null) {
            $this->getTotalPrice();
        }
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
        $this->keep = null;
        $this->keep_id = null;
        $this->cart = null;
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
        $this->keeps = Keep::where('keep_time', '>=', Carbon::now())->where('status', strtolower(KeepStatus::ACTIVE))->pluck('no_keep', 'id')->toArray();
        if ($query) {
            $this->keeps = Keep::where('keep_time', '>=', Carbon::now())
            ->where('status', strtolower(KeepStatus::ACTIVE))
            ->where('no_keep', 'like', '%'.$query.'%')
            ->pluck('no_keep', 'id')
            ->toArray();
        }
    }

    public function searchPreOrder($query)
    {
        $this->preOrders = PreOrder::whereNotIn('id', Sale::pluck('pre_order_id'))->pluck('no_pre_order', 'id')->toArray();
        if ($query) {
            $this->preOrders = PreOrder::whereNotIn('id', Sale::pluck('pre_order_id'))
            ->where('no_pre_order', 'like', '%'.$query.'%')
            ->pluck('no_pre_order', 'id')
            ->toArray();
        }
    }

    public function updatedProductId()
    {
        $this->productStockList = ProductStock::with('color', 'size')->where('product_id', $this->product_id)->get()->toArray();
        $this->productStockList = collect($this->productStockList)->map(function ($stockItem) {
            $stockType = 'pre_order_stock';
            if($this->preOrder) {
                $keepProduct = PreOrderProduct::where('product_stock_id', $stockItem['id'])->where('pre_order_id', $this->pre_order_id)->first();
                $stockItem['all_stock'] += $keepProduct?->total_items;
                $stockItem[$stockType] += $keepProduct?->total_items;
            } else {
                $stockType = $this->group_id == 1 ? 'store_stock' : 'home_stock';
                if ($this->keep) {
                    $keepProduct = KeepProduct::where('product_stock_id', $stockItem['id'])->where('keep_id', $this->keep_id)->first();
                    $stockItem['all_stock'] += $keepProduct?->total_items;
                    $stockItem[$stockType] += $keepProduct?->total_items;
                }
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
            if($this->keep->keep_time <= Carbon::now()) {
                $this->keep = null;
                $this->alert('warning', 'Keep Time Out. Please Refresh the Page');
            } else {
                $this->group_id = $this->keep->customer->group_id;
                $this->customer_id = $this->keep->customer_id;
                $this->cart = [];
                $stockType = $this->group_id == 1 ? 'store_stock' : 'home_stock';
                foreach ($this->keep->keepProducts as $keepProduct) {
                    $this->cart[$keepProduct->product_stock_id] = [
                        'id' => $keepProduct->product_stock_id,
                        'color' => $keepProduct->productStock->color->name,
                        'size' => $keepProduct->productStock->size->name,
                        'product' => $keepProduct->productStock->product->name,
                        'quantity' => $keepProduct->total_items,
                        'all_stock' => $keepProduct->productStock->all_stock + $keepProduct->total_items,
                        $stockType => $keepProduct->productStock->$stockType + $keepProduct->total_items,
                        'selling_price' => $keepProduct->selling_price,
                        'total_price' => $keepProduct->total_price,
                    ];
                }
                $this->getTotalPrice();
            }
        }
    }

    public function updatedPreOrderId()
    {
        if($this->pre_order_id) {
            $this->preOrder = PreOrder::where('id', $this->pre_order_id)->first();
            $this->group_id = $this->preOrder->customer->group_id;
            $this->customer_id = $this->preOrder->customer_id;
            $this->cart = [];
            $stockType = 'pre_order_stock';
            foreach ($this->preOrder->preOrderProducts as $preOrderProduct) {
                $this->cart[$preOrderProduct->product_stock_id] = [
                    'id' => $preOrderProduct->product_stock_id,
                    'color' => $preOrderProduct->productStock->color->name,
                    'size' => $preOrderProduct->productStock->size->name,
                    'product' => $preOrderProduct->productStock->product->name,
                    'quantity' => $preOrderProduct->total_items,
                    'all_stock' => $preOrderProduct->productStock->all_stock + $preOrderProduct->total_items,
                    $stockType => $preOrderProduct->productStock->$stockType + $preOrderProduct->total_items,
                    'selling_price' => $preOrderProduct->selling_price,
                    'total_price' => $preOrderProduct->total_price,
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
        if(strtolower($this->discount_type) === strtolower(DiscountType::PERSEN)) {
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

    public function addToCart($productStockId)
    {
        $stockType = 'pre_order_stock';
        if(!$this->preOrder && $this->sale?->pre_order_id == null) {
            $stockType = $this->group_id == 1 ? 'store_stock' : 'home_stock';
        }
        $productStock = ProductStock::where('id', $productStockId)->first();
        $this->cart[$productStockId]['selling_price'] = $productStock->selling_price;

        if(!array_key_exists($stockType, $this->cart[$productStockId])) {
            $this->cart[$productStockId][$stockType] = ProductStock::where('id', $productStockId)->first()->$stockType;
            $this->cart[$productStockId]['all_stock'] = ProductStock::where('id', $productStockId)->first()->all_stock;
        }

        if($this->cart[$productStockId]['quantity'] > $this->cart[$productStockId][$stockType]) {
            $this->cart[$productStockId]['quantity'] = $this->cart[$productStockId][$stockType];
            $this->alert('warning', 'Stock Not Enough');
        } elseif($this->cart[$productStockId]['quantity'] == null) {
            $this->cart[$productStockId]['quantity'] = 0;
            $this->productStock = $this->cart[$productStockId]['id'];
            $this->deleteProductStock();
        } elseif($this->cart[$productStockId]['quantity'] < 1) {
            $this->cart[$productStockId]['quantity'] = 1;
        }

        if($this->cart[$productStockId]['quantity'] >= 1 && $this->cart[$productStockId]['quantity'] <= $this->cart[$productStockId][$stockType]) {
            $this->cart[$productStockId] = [
                'id' => $productStock->id,
                'color' => $productStock->color->name,
                'size' => $productStock->size->name,
                'product' => $productStock->product->name,
                'quantity' => $this->cart[$productStockId]['quantity'],
                $stockType => $this->cart[$productStockId][$stockType],
                'all_stock' => $this->cart[$productStockId]['all_stock'],
                'purchase_price' => $productStock->purchase_price,
                'selling_price' => $productStock->selling_price,
                'total_price' => $this->cart[$productStockId]['selling_price'] * $this->cart[$productStockId]['quantity']
            ];
            $this->getTotalPrice();
        }

    }

    public function addProductStock($productStockId)
    {
        $stockType = "pre_order_stock";
        if(!$this->preOrder && $this->sale?->pre_order_id == null) {
            $stockType = $this->group_id == 1 ? 'store_stock' : 'home_stock';
        }

        if (!isset($this->cart[$productStockId])) {
            $productStock = ProductStock::where('id', $productStockId)->first();
            if($this->preOrder) {
                $preOrderProduct = $this->preOrder?->preOrderProducts()->where('product_stock_id', $productStockId)->first();
                $this->cart[$productStockId]['pre_order_stock'] = $productStock->pre_order_stock + $preOrderProduct?->total_items;
                $this->cart[$productStockId]['all_stock'] = $productStock->all_stock + $preOrderProduct?->total_items;
            } elseif($this->keep) {
                $keepProduct = $this->keep?->keepProducts()->where('product_stock_id', $productStockId)->first();
                $this->cart[$productStockId]['home_stock'] = $productStock->home_stock + $keepProduct->home_stock;
                $this->cart[$productStockId]['store_stock'] = $productStock->store_stock + $keepProduct->store_stock;
                $this->cart[$productStockId]['all_stock'] = $productStock->all_stock + $keepProduct->total_items;
            } else {
                $this->cart[$productStockId]['home_stock'] = $productStock->home_stock;
                $this->cart[$productStockId]['store_stock'] = $productStock->store_stock;
                $this->cart[$productStockId]['pre_order_stock'] = $productStock->pre_order_stock;
                $this->cart[$productStockId]['all_stock'] = $productStock->all_stock;
            }

            if ($this->cart[$productStockId][$stockType] > 0) {
                $this->cart[$productStockId]['quantity'] = 1;
                $this->addToCart($productStockId);
            } else {
                unset($this->cart[$productStockId]);
                $this->alert('warning', "Out Of Stock");
            }

        } else {
            if($this->cart[$productStockId][$stockType] > $this->cart[$productStockId]['quantity']) {
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
        $this->productStock = null;
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
        $stockType = "pre_order_stock";
        if(!$this->preOrder && $this->sale?->pre_order_id == null) {
            $stockType = $this->group_id == 1 ? 'store_stock' : 'home_stock';
        }
        try {
            $sale = Sale::create([
                'user_id' => Auth::user()->id,
                'keep_id' => $this->keep != null ? $this->keep->id : null,
                'pre_order_id' => $this->preOrder != null ? $this->preOrder->id : null,
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
                'outstanding_balance' => $this->cash_change < 0 ? -1 * $this->cash_change : 0,
                'total_items' => $this->total_items,
                'desc' => $this->desc,
                'marketplace_id' => $this->marketplace_id,
                'order_id_marketplace' => $this->order_id_marketplace,
            ]);

            foreach ($this->cart as $productStock) {
                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_stock_id' => $productStock['id'],
                    'total_items' => $productStock['quantity'],
                    'price' => $productStock['selling_price'],
                    'total_price' => $productStock['total_price']
                ]);
                if($this->keep == null && $this->preOrder == null) {
                    $saleItem->productStock->update([
                        $stockType => $saleItem->productStock->$stockType - $productStock['quantity'],
                        'all_stock' => $saleItem->productStock->all_stock - $productStock['quantity'],
                    ]);
                    setStockHistory(
                        $saleItem->productStock->id,
                        StockActivity::SALES,
                        StockStatus::ADD,
                        NULL,
                        $stockType,
                        $productStock['quantity'],
                        $sale->no_sale,
                        $saleItem->productStock->all_stock,
                        $saleItem->productStock->home_stock,
                        $saleItem->productStock->store_stock,
                        $saleItem->productStock->pre_order_stock,
                    );
                } elseif($this->keep) {
                    $keepProduct = $this->keep->keepProducts->where('product_stock_id', $saleItem->product_stock_id)->first();
                    $keepHistories = ProductStockHistory::where('reference', $this->keep->no_keep)->get();
                    foreach($keepHistories as $keepHistory) {
                        $keepHistory->update([
                            'is_temporary' => false,
                        ]);
                    }

                    if($keepProduct != null) {
                        $additionalStock = $keepProduct->total_items - $saleItem->total_items;
                        $keepProduct->productStock->update([
                            $stockType => $keepProduct->productStock->$stockType + $additionalStock,
                            'all_stock' => $keepProduct->productStock->all_stock + $additionalStock,
                        ]);
                        setStockHistory(
                            $keepProduct->productStock->id,
                            StockActivity::SALES,
                            StockStatus::ADD,
                            NULL,
                            $stockType,
                            $additionalStock,
                            $sale->no_sale,
                            $keepProduct->productStock->all_stock,
                            $keepProduct->productStock->home_stock,
                            $keepProduct->productStock->store_stock,
                            $keepProduct->productStock->pre_order_stock,
                        );
                    } else {
                        $saleItem->productStock->update([
                            $stockType => $saleItem->productStock->$stockType - $saleItem->total_items,
                            'all_stock' => $saleItem->productStock->all_stock - $saleItem->total_items,
                        ]);
                        setStockHistory(
                            $saleItem->productStock->id,
                            StockActivity::SALES,
                            StockStatus::ADD,
                            NULL,
                            $stockType,
                            $saleItem->total_items,
                            $sale->no_sale,
                            $saleItem->productStock->all_stock,
                            $saleItem->productStock->home_stock,
                            $saleItem->productStock->store_stock,
                            $saleItem->productStock->pre_order_stock,
                        );
                    }
                } elseif($this->preOrder) {
                    $preOrderProduct = $this->preOrder->preOrderProducts->where('product_stock_id', $saleItem->product_stock_id)->first();
                    $preOrderHistories = ProductStockHistory::where('reference', $this->preOrder->no_pre_order_id)->get();
                    foreach($preOrderHistories as $preOrderHistory) {
                        $preOrderHistory->update([
                            'is_temporary' => false,
                        ]);
                    }
                    if($preOrderProduct != null) {
                        $additionalStock = $preOrderProduct->total_items - $saleItem->total_items;
                        $preOrderProduct->productStock->update([
                            $stockType => $preOrderProduct->productStock->$stockType + $additionalStock,
                            'all_stock' => $preOrderProduct->productStock->all_stock + $additionalStock,
                        ]);
                        setStockHistory(
                            $preOrderProduct->productStock->id,
                            StockActivity::SALES,
                            StockStatus::ADD,
                            NULL,
                            $stockType,
                            $additionalStock,
                            $sale->no_sale,
                            $preOrderProduct->productStock->all_stock,
                            $preOrderProduct->productStock->home_stock,
                            $preOrderProduct->productStock->store_stock,
                            $preOrderProduct->productStock->pre_order_stock,
                        );
                    } else {
                        $saleItem->productStock->update([
                            $stockType => $saleItem->productStock->$stockType - $saleItem->total_items,
                            'all_stock' => $saleItem->productStock->all_stock - $saleItem->total_items,
                        ]);
                        setStockHistory(
                            $saleItem->productStock->id,
                            StockActivity::SALES,
                            StockStatus::ADD,
                            NULL,
                            $stockType,
                            $saleItem->total_items,
                            $sale->no_sale,
                            $saleItem->productStock->all_stock,
                            $saleItem->productStock->home_stock,
                            $saleItem->productStock->store_stock,
                            $saleItem->productStock->pre_order_stock,
                        );
                    }
                }
            }

            if($this->keep != null) {
                $this->keep->update([
                    'status' => KeepStatus::SOLD
                ]);
                $keepNotSales = $this->keep->keepProducts->whereNotIn('product_stock_id', $sale->saleItems->pluck('product_stock_id')->toArray());
                foreach ($keepNotSales as $keepNotSale) {
                    $keepNotSale->productStock->update([
                        $stockType => $keepNotSale->productStock->$stockType + $keepNotSale->total_items,
                        'all_stock' => $keepNotSale->productStock->all_stock + $keepNotSale->total_items
                    ]);
                    setStockHistory(
                        $keepNotSale->productStock->id,
                        StockActivity::SALES,
                        StockStatus::ADD,
                        NULL,
                        $stockType,
                        $keepNotSale->total_items,
                        $sale->no_sale,
                        $keepNotSale->productStock->all_stock,
                        $keepNotSale->productStock->home_stock,
                        $keepNotSale->productStock->store_stock,
                        $keepNotSale->productStock->pre_order_stock,
                    );
                }
            }

            if($this->preOrder != null) {
                $preOrderNotSales = $this->preOrder->preOrderProducts->whereNotIn('product_stock_id', $sale->saleItems->pluck('product_stock_id')->toArray());
                foreach ($preOrderNotSales as $preOrderNotSale) {
                    $preOrderNotSale->productStock->update([
                        $stockType => $preOrderNotSale->productStock->$stockType + $preOrderNotSale->total_items,
                        'all_stock' => $preOrderNotSale->productStock->all_stock + $preOrderNotSale->total_items
                    ]);
                    setStockHistory(
                        $preOrderNotSale->productStock->id,
                        StockActivity::SALES,
                        StockStatus::ADD,
                        NULL,
                        $stockType,
                        $preOrderNotSale->total_items,
                        $sale->no_sale,
                        $preOrderNotSale->productStock->all_stock,
                        $preOrderNotSale->productStock->home_stock,
                        $preOrderNotSale->productStock->store_stock,
                        $preOrderNotSale->productStock->pre_order_stock,
                    );
                }
            }

            $setting->update([
                'sale_increment' => $setting->sale_increment + 1
            ]);

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
            return redirect()->route('sale');
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

        $stockType = "pre_order_stock";
        if(!$this->preOrder && $this->sale?->pre_order_id == null) {
            $stockType = $this->group_id == 1 ? 'store_stock' : 'home_stock';
        }

        foreach ($this->sale->saleItems as $product) {
            $this->cart[$product->product_stock_id] = [
                'id' => $product->product_stock_id,
                'color' => $product->productStock->color->name,
                'size' => $product->productStock->size->name,
                'product' => $product->productStock->product->name,
                'quantity' => $product->total_items,
                'all_stock' => $product->productStock->all_stock + $product->total_items,
                $stockType => $product->productStock->store_stock + $product->total_items,
                'selling_price' => $product->price,
                'total_price' => $product->total_price
            ];
        }

        $this->discount = $this->sale->discount;
        $this->discount_type = $this->sale->discount_type;
        $this->tax = $this->sale->tax;
        $this->ship = $this->sale->ship;
        $this->payment_type = $this->sale->salePayment?->payment_type->key;
        $this->cash_received = $this->sale->salePayment?->cash_received;
        $this->cash_change = $this->sale->salePayment?->cash_change;
        if(strtolower($this->payment_type) === strtolower(PaymentType::TRANSFER)) {
            $this->payment_type = PaymentType::TRANSFER;
            $this->bank_id = $this->sale->salePayment?->bank_id;
            $this->account_number = $this->sale->salePayment?->account_number;
            $this->account_name = $this->sale->salePayment?->account_name;
        }
    }

    public function update()
    {
        $this->validate();
        $this->sale->update([
            'user_id' => Auth::user()->id,
            'customer_id' => $this->customer_id,
            'term_of_payment_id' => $this->term_of_payment_id,
            'discount_type' => $this->discount_type ?? null,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'ship' => $this->ship,
            'discount_id' => $this->discount_id ?? null,
            'sub_total' => $this->sub_total,
            'total_price' => $this->total_price,
            'outstanding_balance' => $this->cash_change < 0 ? -1 * $this->cash_change : 0,
            'total_items' => $this->total_items,
            'desc' => $this->desc,
            'marketplace_id' => $this->marketplace_id,
            'order_id_marketplace' => $this->order_id_marketplace,
        ]);

        $stockType = "pre_order_stock";
        if(!$this->preOrder && $this->sale?->pre_order_id == null) {
            $stockType = $this->group_id == 1 ? 'store_stock' : 'home_stock';
        }

        foreach ($this->sale->saleItems as $saleItem) {
            $stock = ProductStock::where('id', $saleItem->product_stock_id)->first();
            $stock->update([
                $stockType => $stock->$stockType + $saleItem->total_items,
                'all_stock' => $stock->all_stock + $saleItem->total_items,
            ]);
            setStockHistory(
                $stock->id,
                StockActivity::SALES,
                StockStatus::CHANGE_REMOVE,
                $stockType,
                NULL,
                $saleItem->total_items,
                $this->sale->no_sale,
                $stock->all_stock,
                $stock->home_stock,
                $stock->store_stock,
                $stock->pre_order_stock,
            );
            $saleItem->delete();
        }

        foreach ($this->cart as $productStock) {
            $saleItem = SaleItem::create([
                'sale_id' => $this->sale->id,
                'product_stock_id' => $productStock['id'],
                'total_items' => $productStock['quantity'],
                'price' => $productStock['selling_price'],
                'total_price' => $productStock['total_price']
            ]);

            if($this->keep == null && $this->preOrder == null) {
                $stock = ProductStock::where('id', $saleItem->product_stock_id)->first();
                $stock->update([
                    $stockType => $stock->$stockType - $productStock['quantity'],
                    'all_stock' => $stock->all_stock - $productStock['quantity'],
                ]);

                setStockHistory(
                    $saleItem->productStock->id,
                    StockActivity::SALES,
                    StockStatus::CHANGE_ADD,
                    NULL,
                    $stockType,
                    $productStock['quantity'],
                    $this->sale->no_sale,
                    $stock->all_stock,
                    $stock->home_stock,
                    $stock->store_stock,
                    $stock->pre_order_stock,
                );
            }
        }

        $this->sale->salePayment->update([
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

    public function resetSale()
    {
        $this->sale = null;
        $this->keep = null;
        $this->preOrder = null;
        $this->cart = null;
        $this->customer_id = null;
        $this->group_id = null;
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
