<?php

namespace Database\Seeders;

use App\Models\FinancialCategory;
use Illuminate\Database\Seeder;

class FinancialCategorySeeder extends Seeder
{
    private const CATEGORIES = [
        [
            'name' => 'Quote associative',
            'type' => 'income',
            'area' => 'association',
        ],
        [
            'name' => 'Tesseramento',
            'type' => 'income',
            'area' => 'association',
        ],
        [
            'name' => 'Organizzazione eventi',
            'type' => 'expense',
            'area' => 'social',
        ],
        [
            'name' => 'Utenze',
            'type' => 'expense',
            'area' => 'general',
        ],
    ];

    public function run(): void
    {
        foreach (self::CATEGORIES as $category) {
            FinancialCategory::factory()->create($category);
        }
    }
}
