<?php

// use App\Http\Controllers\Admin\CategoryController;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerDebtController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\SalePageController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\SupplierController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard',
    [DashboardController::class, 'index'])->name('dashboard');

// Route::middleware(['auth'])->group(function () {

//     Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
//     // Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
//     // Route::resource('customers', App\Http\Controllers\Admin\CustomerController::class);
//     // Route::resource('sales', App\Http\Controllers\Admin\SaleController::class);
//     // Route::resource('stocks', App\Http\Controllers\Admin\StockController::class);
//     // Route::resource('customer-debts', App\Http\Controllers\Admin\CustomerDebtController::class);
// });

Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class);
Route::resource('suppliers', SupplierController::class);
Route::resource('customers', CustomerController::class);
//Route::resource('stocks', StockController::class);
Route::resource('purchases', PurchaseController::class);

Route::prefix('stocks')->group(function () {

    Route::get('/movements', [StockController::class, 'movements'])
        ->name('stocks.movements');

    Route::get('/movements/{id}', [StockController::class, 'showMovement'])
        ->name('stocks.movements.show');

    Route::get('/', [StockController::class, 'index'])->name('stocks.index');
    Route::get('/{id}', [StockController::class, 'show'])->name('stocks.show');
    Route::put('/{id}', [StockController::class, 'update'])->name('stocks.update');
});

Route::prefix('sales')->group(function () {
    Route::get('/', [SalePageController::class, 'index'])->name('sales.index');
    Route::get('/create', [SalePageController::class, 'create'])->name('sales.create');
    Route::get('/{id}', [SalePageController::class, 'show'])->name('sales.show');
});

Route::prefix('customer-debts')->group(function () {
    Route::get('/', [CustomerDebtController::class, 'view'])->name('customer-debts.index');
    Route::get('/{id}/detail', [CustomerDebtController::class, 'detail'])->name('customer-debts.detail');
    Route::post('/{id}/pay', [CustomerDebtController::class, 'pay'])->name('customer-debts.pay');

});
