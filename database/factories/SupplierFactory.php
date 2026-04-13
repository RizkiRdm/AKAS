<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'id_supplier' => 'SUP-'.fake()->unique()->numerify('#####'),
            'nama_supplier' => fake()->company(),
            'alamat' => fake()->address(),
            'no_telp' => fake()->phoneNumber(),
        ];
    }
}
