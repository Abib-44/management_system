<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use Illuminate\Database\Seeder;

class ClassRoomSeeder extends Seeder
{
    public function run(): void
    {
        $classes = ['1A', '1B', '2A', '2B', '3A'];

        foreach ($classes as $name) {
            ClassRoom::firstOrCreate(
                ['name' => $name],
                ClassRoom::factory()->make(['name' => $name])->toArray()
            );
        }
    }
}
