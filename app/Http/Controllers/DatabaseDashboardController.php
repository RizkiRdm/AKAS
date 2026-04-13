<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DatabaseDashboardController extends Controller
{
    public function index()
    {
        $status = [];
        $connection = false;
        $error = null;

        try {
            DB::connection()->getPdo();
            $connection = true;
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $tables = [
            'users', 'categories', 'units', 'suppliers', 'products',
            'shifts', 'stock_in', 'sales', 'cash_flow', 'audit_log',
        ];

        $counts = [];
        if ($connection) {
            foreach ($tables as $table) {
                try {
                    $counts[$table] = DB::table($table)->count();
                } catch (\Exception $e) {
                    $counts[$table] = 'Error: '.$e->getMessage();
                }
            }
        }

        // Migration status
        Artisan::call('migrate:status');
        $migrationStatus = Artisan::output();

        // Performance test
        $perfTest = null;
        if ($connection && Product::count() > 0) {
            $perfTest = Benchmark::measure(function () {
                $p = Product::first();
                $originalName = $p->nama_brg;
                $p->update(['nama_brg' => $originalName.' (test)']);
                $p->update(['nama_brg' => $originalName]);
            });
        }

        return view('db-dashboard', compact('connection', 'error', 'counts', 'migrationStatus', 'perfTest'));
    }
}
