<?php

namespace App\Filament\Resources\Materials\Pages;

use App\Filament\Concerns\RedirectsToIndexAfterEdit;
use App\Filament\Resources\Materials\MaterialResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMaterial extends EditRecord
{
    use RedirectsToIndexAfterEdit;

    protected static string $resource = MaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
