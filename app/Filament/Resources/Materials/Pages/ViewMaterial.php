<?php

namespace App\Filament\Resources\Materials\Pages;

use App\Filament\Resources\Materials\MaterialResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMaterial extends ViewRecord
{
    protected static string $resource = MaterialResource::class;

    protected string $view = 'filament.Resources.Materials.material';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
