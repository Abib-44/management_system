<?php

namespace App\Filament\Resources\ServiceAssignments\Pages;

use App\Filament\Concerns\RedirectsToIndexAfterEdit;
use App\Filament\Resources\ServiceAssignments\ServiceAssignmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceAssignment extends EditRecord
{
    use RedirectsToIndexAfterEdit;

    protected static string $resource = ServiceAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
