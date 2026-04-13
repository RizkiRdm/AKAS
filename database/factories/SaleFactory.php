<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'tgl_jual' => now(),
            'id_brg' => Product::factory(),
            'user_id' => User::factory(),
            'shift_id' => Shift::factory(),
            'jumlah' => fake()->numberBetween(1, 10),
            'total_bayar' => fake()->numberBetween(5000, 500000),
            'payment_method' => fake()->randomElement(['cash', 'qris', 'ewallet', 'va']),
            'payment_ref' => fake()->uuid(),
        ];
    }
}
