<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition(): array
    {
        return [
            'id_satuan' => 'SAT-'.fake()->unique()->numerify('#####'),
            'nama_satuan' => fake()->word(),
        ];
    }
}
