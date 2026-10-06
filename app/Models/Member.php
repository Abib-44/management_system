<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_name',
        'first_name',
        'phone',
        'email',
        'registration_date',
        'renewal_date',
        'status',
        'assembly_status',
        'activity_notes',
        'annual_fee',
    ];

    protected function casts(): array
    {
        return [
            'registration_date' => 'date',
            'renewal_date' => 'date',
            'annual_fee' => 'decimal:2',
        ];
    }

    public function membershipFees(): HasMany
    {
        return $this->hasMany(MembershipFee::class);
    }

    public function documentLinks(): HasMany
    {
        return $this->hasMany(
            DocumentArchiveLink::class,
            'member_id'
        );
    }

    /**
     * Totale pagato dal membro.
     */
    public function getPaidAmountAttribute(): float
    {
        return (float) $this->membershipFees()->sum('amount');
    }

    /**
     * Totale ancora da pagare.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(
            0,
            (float) $this->annual_fee - $this->paid_amount
        );
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(
            Attachment::class,
            'attachable'
        );
    }

    /**
     * Stato del pagamento.
     */
    public function getPaymentStatusAttribute(): string
    {
        if ($this->paid_amount <= 0) {
            return 'not_paid';
        }

        if ($this->paid_amount >= (float) $this->annual_fee) {
            return 'paid';
        }

        return 'partially_paid';
    }
}
