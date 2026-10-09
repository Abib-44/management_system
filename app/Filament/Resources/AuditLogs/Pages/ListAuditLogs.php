<?php

namespace App\Filament\Resources\AuditLogs\Pages;

use App\Filament\Resources\AuditLogs\AuditLogResource;
use Filament\Resources\Pages\ListRecords;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * Alterna solo asc e desc, senza il terzo stato "reset"
     * che causava il click a vuoto.
     */
    public function sortTable(?string $column = null, ?string $direction = null): void
    {
        if ($direction === null && $column !== null && $column === $this->getTableSortColumn()) {
            $direction = $this->getTableSortDirection() === 'asc' ? 'desc' : 'asc';
        }

        parent::sortTable($column, $direction);
    }
}