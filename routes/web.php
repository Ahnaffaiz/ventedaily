<?php

use App\Livewire\Customer\Customer;
use App\Livewire\Customer\Group;
use App\Livewire\Dashboard;
use App\Livewire\Product\Category;
use App\Livewire\Product\Color;
use App\Livewire\Product\CreateProduct;
use App\Livewire\Product\Product;
use App\Livewire\Product\Size;
use App\Livewire\Purchase\CreatePurchase;
use App\Livewire\Purchase\ListPurchase;
use App\Livewire\Supplier\Supplier;
use Illuminate\Support\Facades\Route;

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

    //supplier
    Route::get('/supplier', Supplier::class)->name('supplier');

    //customer
    Route::get('/customer', Customer::class)->name('customer');
    Route::get('/group', Group::class)->name('group');

    //purchase
    Route::get('/purchase', ListPurchase::class)->name('purchase');
    Route::get('/create-purchase/{purchase?}', CreatePurchase::class)->name('create-purchase');
});
