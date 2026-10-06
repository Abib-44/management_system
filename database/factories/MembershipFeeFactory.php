<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\MembershipFee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MembershipFee>
 */
class MembershipFeeFactory extends Factory
{
    protected $model = MembershipFee::class;

    public function definition(): array
    {
        $notes = [
            'Quota associativa annuale',
            'Pagamento regolare',
            'Quota relativa al periodo indicato',
            'Pagamento effettuato in sede',
            'Versamento ricevuto',
            'Quota associativa',
            'Pagamento della quota',
            'Versamento effettuato',
            'Pagamento registrato',
            'Quota per attività associative',
            null,
        ];

        $amounts = [
            20.00,
            25.00,
            30.00,
            40.00,
            50.00,
            60.00,
            75.00,
            100.00,
        ];

        $paymentMethods = [
            'cash',
            'bank_transfer',
            'card',
            'other',
        ];

        return [
            'member_id' => Member::factory(),

            'amount' => $amounts[
                array_rand($amounts)
            ],

            'payment_date' => now()->subDays(
                random_int(1, 365)
            ),

            'payment_method' => $paymentMethods[
                array_rand($paymentMethods)
            ],

            'receipt_number' => random_int(0, 1)
                ? 'MF-'.str_pad(
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
