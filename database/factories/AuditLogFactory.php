<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'occurred_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'action' => $this->faker->randomElement(['created', 'updated', 'deleted', 'login', 'logout']),
            'detail' => $this->faker->sentence(),
            'ip_address' => $this->faker->ipv4(),
        ];
    }
}
