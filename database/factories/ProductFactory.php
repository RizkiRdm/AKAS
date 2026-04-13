<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $harga_beli = fake()->numberBetween(1000, 100000);

        return [
            'id_brg' => 'BRG-'.fake()->unique()->numerify('##########'),
            'nama_brg' => fake()->words(3, true),
            'id_kat' => Category::factory(),
            'id_satuan' => Unit::factory(),
            'stok' => fake()->numberBetween(0, 1000),
            'harga_beli' => $harga_beli,
            'harga_jual' => $harga_beli * 1.2,
        ];
    }
}
