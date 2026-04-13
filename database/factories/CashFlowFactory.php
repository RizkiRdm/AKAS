<?php

namespace Database\Factories;

use App\Models\CashFlow;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

class CashFlowFactory extends Factory
{
    protected $model = CashFlow::class;

    public function definition(): array
    {
        return [
            'shift_id' => Shift::factory(),
            'tgl_flow' => now(),
            'keterangan' => fake()->sentence(),
            'masuk' => fake()->randomElement([fake()->numberBetween(10000, 100000), 0]),
            'keluar' => fake()->randomElement([0, fake()->numberBetween(5000, 50000)]),
        ];
    }
}
