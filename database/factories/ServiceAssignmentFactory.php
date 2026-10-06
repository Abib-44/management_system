<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceAssignmentFactory extends Factory
{
    private const KEYS = [
        'Chiave ingresso',
        'Chiave sala principale',
        'Chiave magazzino',
        'Chiave aula 2',
    ];

    private const SERVICES = [
        'Pulizia sala',
        'Manutenzione giardino',
        'Sanificazione locali',
        'Manutenzione impianti',
    ];

    public function definition(): array
    {
        $isKey = fake()->boolean(60);
        $deliveredAt = fake()->dateTimeBetween('-60 days', 'now');
        $returnedAt = fake()->boolean(40) ? fake()->dateTimeBetween($deliveredAt, 'now') : null;

        return [
            'type' => $isKey ? 'key' : 'service',
            'name' => fake()->randomElement($isKey ? self::KEYS : self::SERVICES),
            'assignee_name' => $isKey ? fake()->name() : $this->serviceAssignee(),
            'document_number' => $isKey ? fake()->unique()->numerify('CI-K###') : null,
            'delivered_at' => $deliveredAt,
            'returned_at' => $returnedAt,
            'status' => $this->statusFor($isKey, $returnedAt !== null),
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }

    private function serviceAssignee(): string
    {
        return fake()->boolean() ? 'Servizio esterno' : fake()->company();
    }

    private function statusFor(bool $isKey, bool $isReturned): string
    {
        return match (true) {
            $isKey && $isReturned => 'returned',
            $isKey && fake()->boolean(10) => 'lost',
            $isKey => 'delivered',
            $isReturned => 'completed',
            default => 'active',
        };
    }
}
