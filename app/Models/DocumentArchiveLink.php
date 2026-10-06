<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentArchiveLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_archive_id',
        'entity_type',
        'member_id',
        'activity_id',
        'transaction_id',
        'student_id',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::saving(function (DocumentArchiveLink $link) {
            $link->entity_type = match (true) {
                $link->member_id !== null => 'member',
                $link->activity_id !== null => 'activity',
                $link->transaction_id !== null => 'transaction',
                $link->student_id !== null => 'student',
                default => $link->entity_type,
            };
        });
    }

    public function documentArchive()
    {
        return $this->belongsTo(DocumentArchive::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function transaction()
    {
        return $this->belongsTo(FinancialTransaction::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getEntityAttribute()
    {
        return $this->member
            ?? $this->activity
            ?? $this->transaction
            ?? $this->student;
    }
}
