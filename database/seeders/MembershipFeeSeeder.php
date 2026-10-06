<?php

namespace Database\Seeders;

use App\Models\MembershipFee;
use Illuminate\Database\Seeder;

class MembershipFeeSeeder extends Seeder
{
    public function run(): void
    {
        MembershipFee::factory()->count(30)->create();
    }
}
