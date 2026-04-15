<?php

namespace Database\Factories;

use App\Domain\Models\AuditLog;
use App\Domain\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => 'INSERT',
            'entity' => 'products',
            'entity_id' => fake()->numberBetween(1, 1000),
            'old_data' => null,
            'new_data' => ['name' => 'Test Product'],
        ];
    }
}
