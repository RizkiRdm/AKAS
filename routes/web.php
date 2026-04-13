<?php

use App\Http\Controllers\DatabaseDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/db-dashboard', [DatabaseDashboardController::class, 'index']);
