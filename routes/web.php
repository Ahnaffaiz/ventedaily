<?php

use App\Livewire\Customer\Customer;
use App\Livewire\Customer\Group;
use App\Livewire\Dashboard;
use App\Livewire\Discount;
use App\Livewire\Keep\CreateKeep;
use App\Livewire\Keep\ListKeep;
use App\Livewire\PreOrder\CreatePreOrder;
use App\Livewire\PreOrder\ListPreOrder;
use App\Livewire\Product\Category;
use App\Livewire\Product\Color;
use App\Livewire\Product\CreateProduct;
use App\Livewire\Product\Product;
use App\Livewire\Product\Size;
use App\Livewire\Purchase\CreatePurchase;
use App\Livewire\Purchase\ListPurchase;
use App\Livewire\Retur\CreateRetur;
use App\Livewire\Retur\ListRetur;
use App\Livewire\Sale\CreateSale;
use App\Livewire\Sale\ListSale;
use App\Livewire\Sale\OnlineSales;
use App\Livewire\Sale\Shipping;
use App\Livewire\Sale\Withdrawal;
use App\Livewire\Setting;
use App\Livewire\StockManagement\Dashboard as StockManagementDashboard;
use App\Livewire\StockManagement\ListStock;
use App\Livewire\Supplier\Supplier;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    //product
    Route::get('/product', Product::class)->name('product');
    Route::get('/category', Category::class)->name('category');
    Route::get('/color', Color::class)->name('color');
    Route::get('/size', Size::class)->name('size');

    //stock management
    Route::get('/stock-management', ListStock::class)->name('stock-management');

    //supplier
    Route::get('/supplier', Supplier::class)->name('supplier');

    //customer
    Route::get('/customer', Customer::class)->name('customer');
    Route::get('/group', Group::class)->name('group');

    //purchase
    Route::get('/purchase', ListPurchase::class)->name('purchase');
    Route::get('/create-purchase/{purchase?}', CreatePurchase::class)->name('create-purchase');
    Route::get('/print-payment/{payment}', function () {
        $payment = Session::get('payment');
        $setting = Session::get('setting');
        return view('print.purchase-payment', compact('payment', 'setting'));
    })->name('print-payment');

    //keep
    Route::get('/keep', ListKeep::class)->name('keep');
    Route::get('/create-keep/{keep?}', CreateKeep::class)->name('create-keep');

    //pre order
    Route::get('/pre-order', ListPreOrder::class)->name('pre-order');
    Route::get('/create-pre-order/{preorder?}', CreatePreOrder::class)->name('create-pre-order');
    Route::get('/pre-order/cashier', CreatePreOrder::class)->name('pre-order/cashier');

    //sale
    Route::get('/sale', ListSale::class)->name('sale');
    Route::get('/create-sale/{sale?}', CreateSale::class)->name('create-sale');
    Route::get('/print-sale-payment/{payment}', function () {
        $payment = Session::get('sale-payment');
        $setting = Session::get('setting');
        return view('print.sale-payment', compact('payment', 'setting'));
    })->name('print-sale-payment');

    //ventedaily
    Route::get('/online-sales', OnlineSales::class)->name('online-sales');
    Route::get('/shipping', Shipping::class)->name('shipping');
    Route::get('/withdrawal', Withdrawal::class)->name('withdrawal');

    //retur
    Route::get('/retur', ListRetur::class)->name('retur');
    Route::get('/create-retur', CreateRetur::class)->name('create-retur');
    Route::get('/create-retur/{retur?}', CreateRetur::class)->name('create-retur');
    Route::get('/print-retur-payment/{retur}', function () {
        $retur = Session::get('retur');
        $setting = Session::get('setting');
        return view('print.retur-payment', compact('retur', 'setting'));
    })->name('print-retur-payment');

    //discount
    Route::get('/discount', Discount::class)->name('discount');

    //settings
    Route::get('/settings', Setting::class)->name('settings');
});
