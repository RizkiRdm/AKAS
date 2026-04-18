<?php

use App\Http\Controllers\DatabaseDashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MasterStokController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect('/dashboard');
    });

    Route::get('/dashboard', function () {
        return view('welcome'); // Temporarily welcome for dashboard
    })->name('dashboard');

    Route::controller(MasterStokController::class)->group(function () {
        Route::get('/master-stok', 'index')->name('master-stok.index');

        // Products
        Route::post('/master-stok/products', 'storeProduct')->name('master-stok.products.store');
        Route::put('/master-stok/products/{product}', 'updateProduct')->name('master-stok.products.update');
        Route::delete('/master-stok/products/{product}', 'destroyProduct')->name('master-stok.products.destroy');

        // Categories
        Route::post('/master-stok/categories', 'storeCategory')->name('master-stok.categories.store');
        Route::put('/master-stok/categories/{category}', 'updateCategory')->name('master-stok.categories.update');

        // Units
        Route::post('/master-stok/units', 'storeUnit')->name('master-stok.units.store');

        // Suppliers
        Route::post('/master-stok/suppliers', 'storeSupplier')->name('master-stok.suppliers.store');

        // Stock In
        Route::post('/master-stok/stock-in', 'storeStockIn')->name('master-stok.stock-in.store');
    });

    // User Management (Admin only)
    Route::middleware('can:manage-users')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::get('/db-dashboard', [DatabaseDashboardController::class, 'index']);

    Route::controller(\App\Http\Controllers\SalesController::class)->group(function () {
        Route::get('/sales/pos', 'pos')->name('sales.pos');
        Route::post('/sales', 'store')->name('sales.store');
    });
});
