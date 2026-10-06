<?php

namespace App\Filament\Resources\AuditLogs\Pages;

use App\Filament\Concerns\RedirectsToIndexAfterEdit;
use App\Filament\Resources\AuditLogs\AuditLogResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAuditLog extends EditRecord
{
    use RedirectsToIndexAfterEdit;

    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
