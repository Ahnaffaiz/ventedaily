<?php

use App\Http\Controllers\ExportController;
use App\Livewire\Accounting\MonthlyReport;
use App\Livewire\Accounting\SalesReport;
use App\Livewire\Customer\Customer;
use App\Livewire\Customer\Group;
use App\Livewire\Dashboard;
use App\Livewire\Discount;
use App\Livewire\CostExpense\Expense;
use App\Livewire\Keep\CreateKeep;
use App\Livewire\Keep\ListKeep;
use App\Livewire\PreOrder\CreatePreOrder;
use App\Livewire\PreOrder\ListPreOrder;
use App\Livewire\Product\Category;
use App\Livewire\Product\Color;
use App\Livewire\CostExpense\Cost;
use App\Livewire\User;
use App\Livewire\Role;
use App\Livewire\Product\Product;
use App\Livewire\Product\Size;
use App\Livewire\Product\StockIn\CreateStockIn;
use App\Livewire\Product\StockIn\ListStockIn;
use App\Livewire\Product\TransferStock\CreateTransferStock;
use App\Livewire\Product\TransferStock\ListTransferStock;
use App\Livewire\Public\ProductStock;
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
use App\Livewire\Supplier\Supplier;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/product-stock', ProductStock::class)->name('product-stock');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    RoleMiddleware::class . ':Admin|Sales|Accounting|Warehouse|User'
])->group(function() {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    RoleMiddleware::class . ':Admin|Sales|Accounting'
])->group(function () {

    //sale
    Route::get('/sale', ListSale::class)->name('sale');
    Route::get('/create-sale/{sale?}', CreateSale::class)->name('create-sale')->middleware([PermissionMiddleware::class. ':Create Sale|Update Sale']);
    Route::get('/print-sale-payment/{payment}', function () {
        $payment = Session::get('sale-payment');
        $setting = Session::get('setting');
        return view('print.sale-payment', compact('payment', 'setting'));
    })->name('print-sale-payment');

    //ventedaily
    Route::get('/online-sales', OnlineSales::class)->name('online-sales');
    Route::get('/shipping', Shipping::class)->name('shipping');
    Route::get('/withdrawal', Withdrawal::class)->name('withdrawal');

    //customer
    Route::get('/customer', Customer::class)->name('customer');
    Route::get('/group', Group::class)->name('group');

    //discount
    Route::get('/discount', Discount::class)->name('discount');

    //purchase
    Route::get('/purchase', ListPurchase::class)->name('purchase');
    Route::get('/create-purchase/{purchase?}', CreatePurchase::class)->name('create-purchase')->middleware([PermissionMiddleware::class. ':Create Purchase|Update Purchase']);
    Route::get('/print-payment/{payment}', function () {
        $payment = Session::get('payment');
        $setting = Session::get('setting');
        return view('print.purchase-payment', compact('payment', 'setting'));
    })->name('print-payment');

    //Cost and Expense
    Route::get('/cost', Cost::class)->name('cost');
    Route::get('/expense', Expense::class)->name('expense');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    RoleMiddleware::class . ':Admin'
])->group(function () {
    Route::get('/category', Category::class)->name('category');
    Route::get('/color', Color::class)->name('color');
    Route::get('/size', Size::class)->name('size');
    Route::get('/user', User::class)->name('user');
    Route::get('/role', Role::class)->name('role');
    Route::get('/settings', Setting::class)->name('settings');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    RoleMiddleware::class . ':Admin|Warehouse'
])->group(function () {
    //Management Stock
    Route::get('/transfer-stock', ListTransferStock::class)->name('transfer-stock');
    Route::get('/create-transfer-stock/{transferstock?}', CreateTransferStock::class)->name('create-transfer-stock');
    Route::get('/stock-in', ListStockIn::class)->name('stock-in');
    Route::get('/create-stock-in/{stockin?}', CreateStockIn::class)->name('create-stock-in');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    RoleMiddleware::class . ':Admin|Warehouse|User'
])->group(function () {
    //product
    Route::get('/product', Product::class)->name('product');
    Route::get('/product-stock-history/{productStockId}/{startDate}/{endDate}', [ExportController::class, 'stockHistory'])->name('product-stock-history');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    RoleMiddleware::class . ':Admin|Sales|Warehouse|Accounting'
])->group(function () {
    //retur
    Route::get('/retur', ListRetur::class)->name('retur');
    Route::get('/create-retur/{retur?}', CreateRetur::class)->name('create-retur')->middleware([PermissionMiddleware::class. ':Create Retur|Update Retur']);
    Route::get('/print-retur-payment/{retur}', function () {
        $retur = Session::get('retur');
        $setting = Session::get('setting');
        return view('print.retur-payment', compact('retur', 'setting'));
    })->name('print-retur-payment');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    RoleMiddleware::class . ':Admin|Warehouse|Accounting'
])->group(function () {
    //supplier
    Route::get('/supplier', Supplier::class)->name('supplier');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    RoleMiddleware::class . ':Admin|Sales|User|Warehouse|Accounting'
])->group(function () {
    //keep
    Route::get('/keep', ListKeep::class)->name('keep');
    Route::get('/create-keep/{keep?}', CreateKeep::class)->name('create-keep')->middleware([PermissionMiddleware::class. ':Create Keep|Update Keep']);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    RoleMiddleware::class . ':Admin|Sales|User|Accounting'
])->group(function () {
    //pre order
    Route::get('/pre-order', ListPreOrder::class)->name('pre-order');
    Route::get('/create-pre-order/{preorder?}', CreatePreOrder::class)->name('create-pre-order')->middleware([PermissionMiddleware::class. ':Create Pre Order|Update Pre Order']);
    Route::get('/pre-order/cashier', CreatePreOrder::class)->name('pre-order/cashier');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    RoleMiddleware::class . ':Admin|Accounting'
])->group(function() {
    Route::get('/monthly-report', MonthlyReport::class)->name('monthly-report');
    Route::get('/sales-report', SalesReport::class)->name('sales-report');
    Route::get('/monthly-report-print/{month}', [ExportController::class, 'monthlyReport'])->name('monthly-report-print');
});
