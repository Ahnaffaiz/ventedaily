<?php

use App\Livewire\Customer\Customer;
use App\Livewire\Customer\Group;
use App\Livewire\Dashboard;
use App\Livewire\Discount;
use App\Livewire\Keep\CreateKeep;
use App\Livewire\Keep\ListKeep;
use App\Livewire\Product\Category;
use App\Livewire\Product\Color;
use App\Livewire\Product\CreateProduct;
use App\Livewire\Product\Product;
use App\Livewire\Product\Size;
use App\Livewire\Purchase\CreatePurchase;
use App\Livewire\Purchase\ListPurchase;
use App\Livewire\Purchase\PurchasePayment;
use App\Livewire\Sale\CreateSale;
use App\Livewire\Sale\ListSale;
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

    //sale
    Route::get('/sale', ListSale::class)->name('sale');
    Route::get('/create-sale/{sale?}', CreateSale::class)->name('create-sale');
    Route::get('/print-sale-payment/{payment}', function () {
        $payment = Session::get('sale-payment');
        $setting = Session::get('setting');
        return view('print.sale-payment', compact('payment', 'setting'));
    })->name('print-sale-payment');

    //discount
    Route::get('/discount', Discount::class)->name('discount');

    //settings
    Route::get('/settings', Setting::class)->name('settings');
});
