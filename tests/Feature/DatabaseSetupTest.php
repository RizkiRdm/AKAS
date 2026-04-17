<?php

namespace Tests\Feature;

use App\Domain\Models\Product;
use App\Domain\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSetupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if all tables exist.
     */
    public function test_all_tables_exist(): void
    {
        $tables = [
            'users', 'categories', 'units', 'suppliers', 'products',
            'shifts', 'stock_in', 'sales', 'cash_flow', 'audit_log',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table $table does not exist.");
        }
    }

    /**
     * Test products stok check constraint.
     */
    public function test_products_stok_check_constraint(): void
    {
        $this->expectException(QueryException::class);

        $user = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();

        DB::table('products')->insert([
            'name' => 'Negative Stok',
            'sku' => 'TEST-NEG-STOK',
            'category_id' => $product->category_id,
            'unit_id' => $product->unit_id,
            'stok' => -1, // Should trigger check constraint
            'purchase_price' => 100,
            'price' => 120,
        ]);
    }

    /**
     * Test performance CRUD < 200ms.
     */
    public function test_crud_performance(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $p = Product::factory()->create();

        $time = Benchmark::measure(function () use ($p) {
            $originalName = $p->name;
            $p->update(['name' => $originalName.' (test)']);
            $p->update(['name' => $originalName]);
        });

        $this->assertLessThan(200, $time, "CRUD performance is too slow: {$time}ms");
    }

    /**
     * Test database dashboard is accessible.
     */
    public function test_dashboard_is_accessible(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user)->get('/db-dashboard');
        $response->assertStatus(200);
        $response->assertSee('Database');
        $response->assertSee('Dashboard');
    }
}
