<?php

namespace Database\Factories;

use App\Domain\Models\Shift;
use App\Domain\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftFactory extends Factory
{
    protected $model = Shift::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'starting_float' => 500000,
            'ending_cash' => 0,
            'expected_cash' => 0,
            'status' => 'open',
        ];
    }
}
