<?php

namespace App\Filament\Resources\FinancialCategories\Pages;

use App\Filament\Resources\FinancialCategories\FinancialCategoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFinancialCategory extends ViewRecord
{
    protected static string $resource = FinancialCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
