<?php

use App\Http\Controllers\Admin\{ RoleController, UserController, CategoryController, ProductController, CustomerController, StockController, CustomerDebtController };
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

//use App\Http\Controllers\API\CategoryController as APICategoryController;


//Route::post('/purchases', [PurchaseController::class, 'store']);
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::prefix('sales')->group(function () {
    Route::get('/', [SaleController::class, 'index']);
    Route::post('/', [SaleController::class, 'store']);
    Route::get('/{id}', [SaleController::class, 'show']);
    Route::delete('/{id}', [SaleController::class, 'destroy']);
});


Route::middleware(['auth:sanctum', 'role:super_admin,admin'])->group(function () {

    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::get('/{id}', [RoleController::class, 'show']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'destroy']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::put('/{id}/reset-password', [UserController::class, 'resetPassword']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    //Route::apiResource('categories', APICategoryController::class);
    //Route::apiResource('products', ProductController::class);
});


// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::apiResource('customers', CustomerController::class);
// });


// Route::middleware(['auth:sanctum', 'role:kasir,admin,super_admin'])->group(function () {
//     Route::apiResource('sales', SaleController::class)->only(['index', 'store', 'show']);
// });

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('debts')->group(function () {
        Route::get('/', [CustomerDebtController::class, 'index']);
        Route::put('/{id}/pay', [CustomerDebtController::class, 'pay']);
        Route::get('/total', [CustomerDebtController::class, 'total']);
    });
});



Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('forecast')->group(function () {
        Route::get('/data', [SaleController::class, 'forecastData']);
        Route::get('/', [SaleController::class, 'forecast']);
    });
});
