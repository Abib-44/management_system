<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceAssignment extends Model
{
    use HasFactory;

    public const TYPE_LABELS = [
        'key' => 'Chiave',
        'service' => 'Servizio',
    ];

    public const TYPE_COLORS = [
        'key' => 'primary',
        'service' => 'gray',
    ];

    public const TYPE_ICONS = [
        'key' => 'heroicon-o-key',
        'service' => 'heroicon-o-wrench-screwdriver',
    ];

    public const STATUS_LABELS = [
        'key' => [
            'delivered' => 'Consegnata',
            'returned' => 'Restituita',
            'lost' => 'Smarrita',
        ],
        'service' => [
            'active' => 'Attiva',
            'completed' => 'Conclusa',
        ],
    ];

    public const STATUS_COLORS = [
        'delivered' => 'warning',
        'returned' => 'success',
        'lost' => 'danger',
        'active' => 'info',
        'completed' => 'success',
    ];

    protected $fillable = [
        'type',
        'name',
        'assignee_name',
        'document_number',
        'delivered_at',
        'returned_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'delivered_at' => 'date',
            'returned_at' => 'date',
        ];
    }

    public static function statusLabelsFor(?string $type): array
    {
        return $type === null ? [] : (self::STATUS_LABELS[$type] ?? []);
    }

    public static function allStatusLabels(): array
    {
        return array_merge(...array_values(self::STATUS_LABELS));
    }
}
