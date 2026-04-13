<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'table_name' => fake()->randomElement(['products', 'sales', 'stock_in']),
            'record_id' => fake()->numerify('#####'),
            'action' => fake()->randomElement(['INSERT', 'UPDATE', 'DELETE']),
            'old_data' => null,
            'new_data' => ['field' => 'value'],
            'user_id' => User::factory(),
        ];
    }
}
