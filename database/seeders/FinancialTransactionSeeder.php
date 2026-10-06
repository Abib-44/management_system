<?php

namespace Database\Seeders;

use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use Illuminate\Database\Seeder;

class FinancialTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = FinancialCategory::pluck('id')->toArray();

        foreach (range(1, 50) as $i) {
            FinancialTransaction::factory()->create([
                'category_id' => fake()->randomElement($categoryIds),
            ]);
        }
    }
}
