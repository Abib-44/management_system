<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_room_id',
        'first_name',
        'last_name',
        'birth_date',
        'father_name',
        'mother_name',
        'mother_phone',
        'father_phone',
        'address',
        'email',
        'total_fee',
        'installment_plan',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'total_fee' => 'decimal:2',
        ];
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(
            Attachment::class,
            'attachable'
        );
    }

    public function documentLinks(): HasMany
    {
        return $this->hasMany(
            DocumentArchiveLink::class,
            'student_id'
        );
    }

    public function payments(): HasMany
    {
        return $this->hasMany(StudentPayment::class);
    }

    /**
     * Totale pagato dallo studente.
     */
    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    /**
     * Totale ancora da pagare.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(
            0,
            (float) $this->total_fee - $this->paid_amount
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

        if ($this->paid_amount >= (float) $this->total_fee) {
            return 'paid';
        }

        return 'partially_paid';
    }
}
