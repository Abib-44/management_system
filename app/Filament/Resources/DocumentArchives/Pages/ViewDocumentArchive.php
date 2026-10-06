<?php

namespace App\Filament\Resources\DocumentArchives\Pages;

use App\Filament\Resources\DocumentArchives\DocumentArchiveResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDocumentArchive extends ViewRecord
{
    protected static string $resource = DocumentArchiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
