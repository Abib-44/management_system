<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date', 'type', 'category_id', 'description',
        'amount', 'payment_method', 'scope', 'receipt_number', 'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(FinancialCategory::class, 'category_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documentLinks()
    {
        return $this->hasMany(DocumentArchiveLink::class, 'transaction_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(
            Attachment::class,
            'attachable'
        );
    }
}
