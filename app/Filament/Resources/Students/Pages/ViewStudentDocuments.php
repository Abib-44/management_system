<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Filament\Widgets\StudentDocumentsTable;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewStudentDocuments extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected static ?string $title = 'Documenti';

    public function getHeaderWidgets(): array
    {
        return [
            StudentDocumentsTable::make([
                'studentId' => $this->record->id,
            ]),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([]);
    }
}
