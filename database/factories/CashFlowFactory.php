<?php

namespace Database\Factories;

use App\Domain\Models\CashFlow;
use App\Domain\Models\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

class CashFlowFactory extends Factory
{
    protected $model = CashFlow::class;

    public function definition(): array
    {
        return [
            'shift_id' => Shift::factory(),
            'type' => fake()->randomElement(['in', 'out']),
            'amount' => fake()->numberBetween(5000, 100000),
            'source' => fake()->word(),
        ];
    }
}
