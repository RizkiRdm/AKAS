<?php

namespace Database\Factories;

use App\Domain\Models\Category;
use App\Domain\Models\Product;
use App\Domain\Models\Supplier;
use App\Domain\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'unit_id' => Unit::factory(),
            'supplier_id' => Supplier::factory(),
            'name' => fake()->words(3, true),
            'sku' => fake()->unique()->numerify('SKU-##########'),
            'price' => fake()->numberBetween(1000, 500000),
            'stok' => fake()->numberBetween(0, 100),
        ];
    }
}
