<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Generic users without application roles.
         */
        User::factory(10)->create();

        /*
         * Application roles.
         */
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        User::factory()->secretary()->create([
            'name' => 'Secretary User',
            'email' => 'secretary@example.com',
        ]);

        User::factory()->treasurer()->create([
            'name' => 'Treasurer User',
            'email' => 'treasurer@example.com',
        ]);

        User::factory()->teacher()->create([
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
        ]);

        User::factory()->services()->create([
            'name' => 'Services User',
            'email' => 'services@example.com',
        ]);

        /*
         * Development account.
         */
        User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
        ]);

        /*
         * Main development/test account.
         */
        User::factory()->superAdmin()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
