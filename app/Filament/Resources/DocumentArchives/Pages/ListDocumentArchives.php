<?php

namespace App\Filament\Resources\DocumentArchives\Pages;

use App\Filament\Resources\DocumentArchives\DocumentArchiveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentArchives extends ListRecords
{
    protected static string $resource = DocumentArchiveResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
