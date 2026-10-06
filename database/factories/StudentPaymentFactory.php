<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\StudentPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentPayment>
 */
class StudentPaymentFactory extends Factory
{
    protected $model = StudentPayment::class;

    public function definition(): array
    {
        $paymentMethods = [
            'cash',
            'bank_transfer',
            'card',
            'check',
            'other',
        ];

        $notes = [
            'Pagamento registrato',
            'Rata pagata regolarmente',
            'Versamento ricevuto',
            'Pagamento effettuato',
            'Nessuna nota particolare',
            'Pagamento della rata',
            'Quota ricevuta',
            null,
        ];

        return [
            'student_id' => Student::factory(),

            'installment_number' => random_int(1, 4),

            'description' => 'Pagamento rata',

            'amount' => [
                50.00,
                75.00,
                100.00,
                125.00,
                150.00,
                200.00,
                250.00,
                300.00,
            ][array_rand([
                50.00,
                75.00,
                100.00,
                125.00,
                150.00,
                200.00,
                250.00,
                300.00,
            ])],

            'payment_date' => now()->subDays(
                random_int(1, 365)
            ),

            'payment_method' => $paymentMethods[
                array_rand($paymentMethods)
            ],

            'receipt_number' => random_int(0, 4) > 0
                ? 'SP-'.str_pad(
                    (string) random_int(1, 9999),
                    4,
                    '0',
                    STR_PAD_LEFT
                )
                : null,

            'notes' => $notes[array_rand($notes)],
        ];
    }
}
