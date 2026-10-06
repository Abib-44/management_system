<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolYearFactory extends Factory
{
    public function definition(): array
    {
        $startYear = $this->faker->numberBetween(2022, 2026);

        return [
            'name' => "{$startYear}/".($startYear + 1),
            'start_date' => "{$startYear}-09-01",
            'end_date' => ($startYear + 1).'-06-30',
            'is_active' => false,
        ];
    }
}
