<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'id_kat' => 'KAT-'.fake()->unique()->numerify('#####'),
            'nama_kat' => fake()->word(),
        ];
    }
}
