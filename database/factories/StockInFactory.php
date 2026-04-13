<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockInFactory extends Factory
{
    protected $model = StockIn::class;

    public function definition(): array
    {
        return [
            'tgl_masuk' => now(),
            'id_supplier' => Supplier::factory(),
            'id_brg' => Product::factory(),
            'jumlah' => fake()->numberBetween(1, 100),
            'total_harga' => fake()->numberBetween(10000, 1000000),
        ];
    }
}
