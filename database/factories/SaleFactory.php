<?php

namespace Database\Factories;

use App\Domain\Models\Sale;
use App\Domain\Models\Shift;
use App\Domain\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'shift_id' => Shift::factory(),
            'user_id' => User::factory(),
            'total' => fake()->numberBetween(10000, 500000),
            'payment_method' => 'cash',
            'payment_status' => 'completed',
        ];
    }
}
