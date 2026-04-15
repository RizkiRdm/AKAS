<?php

use App\Http\Controllers\DatabaseDashboardController;
use App\Http\Controllers\MasterStokController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', function () {
    return view('welcome'); // Temporarily welcome for dashboard
});

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

Route::get('/db-dashboard', [DatabaseDashboardController::class, 'index']);
