<?php

namespace Database\Seeders;

use App\Domain\Models\Category;
use App\Domain\Models\Product;
use App\Domain\Models\Supplier;
use App\Domain\Models\Unit;
use App\Domain\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        User::factory()->create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'nama_pegawai' => 'Administrator',
            'role' => 'admin',
        ]);

        // Create Cashier
        User::factory()->create([
            'username' => 'cashier',
            'password' => Hash::make('cashier123'),
            'nama_pegawai' => 'Kasir Utama',
            'role' => 'cashier',
        ]);

        // Create 20 products with categories, units, and suppliers
        Category::factory(5)->create()->each(function ($category) {
            $units = Unit::factory(3)->create();
            $suppliers = Supplier::factory(2)->create();

            Product::factory(4)->create([
                'category_id' => $category->id,
                'unit_id' => $units->random()->id,
                'supplier_id' => $suppliers->random()->id,
            ]);
        });
    }
}
