<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Support\Benchmark;

class DatabaseSetupTest extends TestCase
{
    /**
     * Test if all tables exist.
     */
    public function test_all_tables_exist(): void
    {
        $tables = [
            'users', 'categories', 'units', 'suppliers', 'products', 
            'shifts', 'stock_in', 'sales', 'cash_flow', 'audit_log'
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table $table does not exist.");
        }
    }

    /**
     * Test seed data counts.
     */
    public function test_seed_data_counts(): void
    {
        // We seeded 2000 per table in previous steps.
        // I'll check if they have at least 2000.
        $this->assertGreaterThanOrEqual(2000, DB::table('products')->count());
        $this->assertGreaterThanOrEqual(2000, DB::table('users')->count());
        $this->assertGreaterThanOrEqual(2000, DB::table('sales')->count());
    }

    /**
     * Test CHECK constraint on products.stok.
     */
    public function test_products_stok_check_constraint(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        $p = Product::first();
        DB::table('products')->insert([
            'id_brg' => 'TEST-NEG-STOK',
            'nama_brg' => 'Negative Stok',
            'id_kat' => $p->id_kat,
            'id_satuan' => $p->id_satuan,
            'stok' => -1, // Should trigger check constraint
            'harga_beli' => 100,
            'harga_jual' => 120,
        ]);
    }

    /**
     * Test performance CRUD < 200ms.
     */
    public function test_crud_performance(): void
    {
        $time = Benchmark::measure(function () {
            $p = Product::first();
            $originalName = $p->nama_brg;
            $p->update(['nama_brg' => $originalName . ' (test)']);
            $p->update(['nama_brg' => $originalName]);
        });

        $this->assertLessThan(200, $time, "CRUD performance is too slow: {$time}ms");
    }

    /**
     * Test database dashboard is accessible.
     */
    public function test_dashboard_is_accessible(): void
    {
        $response = $this->get('/db-dashboard');
        $response->assertStatus(200);
        $response->assertSee('Database');
        $response->assertSee('Dashboard');
    }
}
