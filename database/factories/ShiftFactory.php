<?php

namespace Database\Factories;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftFactory extends Factory
{
    protected $model = Shift::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'start_time' => now()->subHours(8),
            'end_time' => now(),
            'starting_float' => 500000,
            'calculated_cash_flow' => 1000000,
            'ending_cash' => 1500000,
            'status' => 'closed',
        ];
    }
}
