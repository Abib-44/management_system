<?php

namespace App\Filament\Resources\FinancialCategories\Pages;

use App\Filament\Concerns\RedirectsToIndexAfterEdit;
use App\Filament\Resources\FinancialCategories\FinancialCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditFinancialCategory extends EditRecord
{
    use RedirectsToIndexAfterEdit;

    protected static string $resource = FinancialCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
