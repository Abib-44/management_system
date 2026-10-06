<?php

namespace Database\Factories;

use App\Models\FinancialCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FinancialCategory>
 */
class FinancialCategoryFactory extends Factory
{
    protected $model = FinancialCategory::class;

    public function definition(): array
    {
        $categories = [
            [
                'name' => 'Quote associative',
                'type' => 'income',
                'area' => 'association',
            ],
            [
                'name' => 'Donazioni',
                'type' => 'income',
                'area' => 'association',
            ],
            [
                'name' => 'Contributi',
                'type' => 'income',
                'area' => 'general',
            ],
            [
                'name' => 'Iscrizioni',
                'type' => 'income',
                'area' => 'association',
            ],
            [
                'name' => 'Attività sociali',
                'type' => 'income',
                'area' => 'social',
            ],
            [
                'name' => 'Materiale didattico',
                'type' => 'expense',
                'area' => 'social',
            ],
            [
                'name' => 'Materiale ufficio',
                'type' => 'expense',
                'area' => 'management',
            ],
            [
                'name' => 'Affitto',
                'type' => 'expense',
                'area' => 'management',
            ],
            [
                'name' => 'Utenze',
                'type' => 'expense',
                'area' => 'management',
            ],
            [
                'name' => 'Assicurazione',
                'type' => 'expense',
                'area' => 'management',
            ],
            [
                'name' => 'Manutenzione',
                'type' => 'expense',
                'area' => 'management',
            ],
            [
                'name' => 'Trasporti',
                'type' => 'expense',
                'area' => 'general',
            ],
            [
                'name' => 'Eventi',
                'type' => 'expense',
                'area' => 'association',
            ],
            [
                'name' => 'Formazione',
                'type' => 'expense',
                'area' => 'social',
            ],
            [
                'name' => 'Spese bancarie',
                'type' => 'expense',
                'area' => 'management',
            ],
        ];

        return fake()->randomElement($categories);
    }
}
