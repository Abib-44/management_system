<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $classRoomIds = ClassRoom::query()->pluck('id')->toArray();

        if (empty($classRoomIds)) {
            $classRoomIds = ClassRoom::factory()
                ->count(6)
                ->create()
                ->pluck('id')
                ->toArray();
        }

        Student::factory()
            ->count(30)
            ->state(fn () => [
                'class_room_id' => fake()->randomElement($classRoomIds),
            ])
            ->create();
    }
}
