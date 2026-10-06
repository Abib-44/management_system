<?php

namespace Database\Factories;

use App\Models\DocumentArchive;
use App\Models\DocumentCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentArchive>
 */
class DocumentArchiveFactory extends Factory
{
    protected $model = DocumentArchive::class;

    public function definition(): array
    {
        $documentDate = fake()->dateTimeBetween(
            '-2 years',
            'now'
        );

        $userId = User::query()
            ->inRandomOrder()
            ->value('id')
            ?? User::factory()->create()->id;

        $categoryId = DocumentCategory::query()
            ->where('active', true)
            ->inRandomOrder()
            ->value('id');

        if (! $categoryId) {
            $categoryId = DocumentCategory::factory()->create()->id;
        }

        $titles = [
            'Bilancio 2025',
            'Bilancio 2026',
            'Piano Annuale',
            'Piano Attività',
            'Rinnovo Contratto',
            'Nuovo Contratto',
            'Contratto Affitto',
            'Accordo Collaborazione',
            'Domanda Iscrizione',
            'Richiesta Rinnovo',
            'Richiesta Permesso',
            'Autorizzazione Sede',
            'Delega Rappresentanza',
            'Dati Anagrafici',
            'Elenco Soci',
            'Elenco Partecipanti',
            'Registro Presenze',
            'Programma Annuale',
            'Calendario Attività',
            'Progetto 2026',
            'Progetto Sociale',
            'Relazione Annuale',
            'Relazione Attività',
            'Rendiconto 2025',
            'Rendiconto 2026',
            'Quota Annuale',
            'Pagamento 2026',
            'Rimborso Spese',
            'Spese Trasferta',
            'Richiesta Rimborso',
            'Dati Bancari',
            'Coordinate Bancarie',
            'Documento Identità',
            'Carta Identità',
            'Codice Fiscale',
            'Tessera Associativa',
            'Scheda Iscrizione',
            'Scheda Personale',
            'Piano Finanziario',
            'Preventivo 2026',
            'Consuntivo 2025',
            'Consuntivo 2026',
            'Ordine Servizio',
            'Avviso Ufficiale',
            'Nota Informativa',
            'Comunicazione Interna',
            'Richiesta Informazioni',
            'Domanda Autorizzazione',
            'Esito Verifica',
            'Conferma Pagamento',
        ];

        return [
            'title' => fake()->randomElement($titles),

            'document_category_id' => $categoryId,

            'document_date' => $documentDate,

            'expires_at' => fake()->optional(0.7)->dateTimeBetween(
                $documentDate,
                '+3 years'
            ),

            'status' => fake()->randomElement([
                'active',
                'active',
                'active',
                'archived',
                'expired',
            ]),

            'created_by' => $userId,

            'updated_by' => $userId,
        ];
    }
}
