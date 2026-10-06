<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClassRoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                '1A',
                '1B',
                '2A',
                '2B',
                '3A',
            ]),
            'responsible' => $this->faker->name(),
        ];
    }
}
