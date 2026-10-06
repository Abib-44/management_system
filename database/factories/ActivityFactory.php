<?php

namespace Database\Factories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        $activity = fake()->randomElement([
            [
                'title' => 'Laboratorio estivo',
                'location' => 'Sede',
                'status' => 'in_progress',
                'notes' => 'Attività per bambini',
            ],
            [
                'title' => 'Calcio domenica',
                'location' => 'Campo sportivo',
                'status' => 'scheduled',
                'notes' => 'Attività esterna',
            ],
            [
                'title' => 'Lezione adulti',
                'location' => 'Sala principale',
                'status' => 'scheduled',
                'notes' => 'Lezione dedicata agli adulti',
            ],
            [
                'title' => 'Laboratorio creativo',
                'location' => 'Aula 2',
                'status' => 'scheduled',
                'notes' => 'Attività manuale e creativa',
            ],
            [
                'title' => 'Torneo interno',
                'location' => 'Campo sportivo',
                'status' => 'scheduled',
                'notes' => 'Torneo riservato agli iscritti',
            ],
        ]);

        return [
            'title' => $activity['title'],
            'activity_date' => $this->dateFor($activity['status']),
            'location' => $activity['location'],
            'status' => $activity['status'],
            'responsible_name' => fake()->randomElement([
                'Marco Bianchi',
                'Giulia Ferrari',
                'Luca Romano',
                'Elena Conti',
                null,
            ]),
            'notes' => $activity['notes'],
        ];
    }

    public function scheduled(): static
    {
        return $this->withStatus('scheduled');
    }

    public function inProgress(): static
    {
        return $this->withStatus('in_progress');
    }

    public function completed(): static
    {
        return $this->withStatus('completed');
    }

    public function cancelled(): static
    {
        return $this->withStatus('cancelled');
    }

    private function withStatus(string $status): static
    {
        return $this->state(fn (): array => [
            'status' => $status,
            'activity_date' => $this->dateFor($status),
        ]);
    }

    private function dateFor(string $status): string
    {
        return match ($status) {
            'in_progress' => now()->toDateString(),
            'completed' => now()->subDays(fake()->numberBetween(1, 60))->toDateString(),
            default => now()->addDays(fake()->numberBetween(1, 30))->toDateString(),
        };
    }
}
