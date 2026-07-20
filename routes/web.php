<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerDebtController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\ForecastController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfitabilitySimulationController;
use App\Http\Controllers\Admin\ProfitAnalysisController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\PurchaseRecommendationController;
use App\Http\Controllers\Admin\PurchaseReportController;
use App\Http\Controllers\Admin\SalePageController;
use App\Http\Controllers\Admin\SaleReportController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::post('/change-password',[UserController::class, 'changePassword'])->name('change.password');

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::put('/users/{id}/status', [UserController::class, 'changeStatus'])->name('users.changeStatus');
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/check-email', [UserController::class, 'checkEmail'])->name('users.check-email');
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']);
});

/*
|--------------------------------------------------------------------------
| ADMIN ONLY
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::get('/supplier-products/{supplier}', [PurchaseController::class, 'getProductsBySupplier'])->name('supplier.products');
    Route::resource('purchases', PurchaseController::class);
    Route::resource('discounts', DiscountController::class);
    Route::patch('/discounts/{id}/toggle', [DiscountController::class, 'toggle'])->name('discounts.toggle');

    Route::prefix('stocks')->group(function () {
        Route::get('/movements', [StockController::class, 'movements'])->name('stocks.movements');
        Route::get('/movements/{id}', [StockController::class, 'showMovement'])->name('stocks.movements.show');
        Route::put('/{id}', [StockController::class, 'update'])->name('stocks.update');
    });

    Route::prefix('reports')->group(function () {
        Route::get('/purchases', [PurchaseReportController::class, 'index'])->name('reports.purchases');
        Route::get('/reports/purchases/export-pdf', [PurchaseReportController::class, 'exportPdf'])->name('reports.purchases.pdf');
        Route::get('/reports/purchases/export-excel', [PurchaseReportController::class, 'exportExcel'])->name('reports.purchases.excel');
        Route::get('/reports/profit', [ProfitAnalysisController::class, 'index'])->name('reports.profit');
        Route::get('/reports/profit/export-pdf', [ProfitAnalysisController::class, 'exportPdf'])->name('reports.profit.pdf');
    });

    Route::prefix('forecast')->group(function () {
        Route::get('/', [ForecastController::class, 'index'])->name('forecast.index');
        Route::get('/recommendation', [PurchaseRecommendationController::class, 'index'])->name('forecast.recommendation');
        Route::post('/generate', [PurchaseRecommendationController::class, 'generateForecast'])->name('forecast.generate');
        Route::get('/profitability', [ProfitabilitySimulationController::class, 'index'])->name('forecast.simulation');
    });
});


/*
|--------------------------------------------------------------------------
| ADMIN + CASHIER
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,cashier'])->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::prefix('customer-debts')->group(function () {
        Route::get('/', [CustomerDebtController::class, 'view'])->name('customer-debts.index');
        Route::get('/{id}/detail', [CustomerDebtController::class, 'detail'])->name('customer-debts.detail');
        Route::post('/{id}/pay', [CustomerDebtController::class, 'pay'])->name('customer-debts.pay');
    });
    Route::prefix('sales')->group(function () {
        Route::get('/', [SalePageController::class, 'index'])->name('sales.index');
        Route::get('/create', [SalePageController::class, 'create'])->name('sales.create');
        Route::get('/{id}', [SalePageController::class, 'show'])->name('sales.show');
        Route::get('/{sale}/receipt', [SalePageController::class, 'receipt'])->name('sales.receipt');
        Route::get('/{id}/edit', [SalePageController::class, 'edit'])->name('sales.edit');
        Route::put('/{id}', [SalePageController::class, 'update'])->name('sales.update');
    });

    Route::prefix('stocks')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('stocks.index');
        Route::get('/{id}', [StockController::class, 'show'])->name('stocks.show');
    });

    Route::prefix('reports')->group(function () {
        Route::get('/sales', [SaleReportController::class, 'index'])->name('reports.sales');
        Route::get('/sales/export-pdf', [SaleReportController::class, 'exportPdf'])->name('reports.sales.pdf');
        Route::get('/sales/export-excel', [SaleReportController::class, 'exportExcel'])->name('reports.sales.excel');
    });
});

Route::middleware(['auth', 'role:cashier,admin'])
    ->get('/sales-data', [SaleController::class, 'index']);
