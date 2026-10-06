<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    public const STATUS_LABELS = [
        'scheduled' => 'Pianificata',
        'in_progress' => 'In corso',
        'completed' => 'Completata',
        'cancelled' => 'Annullata',
    ];

    public const STATUS_COLORS = [
        'scheduled' => 'info',
        'in_progress' => 'warning',
        'completed' => 'success',
        'cancelled' => 'danger',
    ];

    public const STATUS_ICONS = [
        'scheduled' => 'heroicon-o-clock',
        'in_progress' => 'heroicon-o-arrow-path',
        'completed' => 'heroicon-o-check-circle',
        'cancelled' => 'heroicon-o-x-circle',
    ];

    protected $fillable = [
        'title',
        'activity_date',
        'location',
        'status',
        'responsible_name',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
        ];
    }

    public static function responsibleOptions(): array
    {
        return static::query()
            ->whereNotNull('responsible_name')
            ->distinct()
            ->orderBy('responsible_name')
            ->pluck('responsible_name', 'responsible_name')
            ->all();
    }
}
