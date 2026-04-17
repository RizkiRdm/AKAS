<?php

namespace Database\Factories;

use App\Domain\Models\Product;
use App\Domain\Models\StockIn;
use App\Domain\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockInFactory extends Factory
{
    protected $model = StockIn::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'qty' => fake()->numberBetween(1, 100),
            'note' => fake()->sentence(),
        ];
    }
}
