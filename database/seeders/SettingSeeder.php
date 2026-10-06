<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'app_name' => 'Gestionale',
            'default_currency' => 'EUR',
            'timezone' => 'Europe/Rome',
        ];

        foreach ($settings as $key => $value) {
            Setting::factory()->create(compact('key', 'value'));
        }
    }
}
