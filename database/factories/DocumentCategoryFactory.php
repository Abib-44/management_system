<?php

namespace Database\Factories;

use App\Models\DocumentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DocumentCategory>
 */
class DocumentCategoryFactory extends Factory
{
    protected $model = DocumentCategory::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Contratti',
            'Fatture',
            'Certificati',
            'Documenti personali',
            'Autorizzazioni',
            'Verbali',
            'Ricevute',
            'Moduli',
            'Comunicazioni',
            'Documentazione varia',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'active' => true,
        ];
    }
}
