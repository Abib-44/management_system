<?php

namespace Database\Factories;

use App\Models\FinancialCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'transaction_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'type' => $this->faker->randomElement(['income', 'expense']),
            'category_id' => FinancialCategory::factory(),
            'description' => $this->faker->sentence(4),
            'amount' => $this->faker->randomFloat(2, 5, 2000),
            'payment_method' => $this->faker->randomElement(['cash', 'bank_transfer', 'card', 'check', 'other']),
            'scope' => $this->faker->randomElement(['all', 'treasury', 'school']),
            'receipt_number' => $this->faker->optional()->bothify('RC-####'),
            'created_by' => User::inRandomOrder()->value('id') ?? User::factory(),
        ];
    }
}
