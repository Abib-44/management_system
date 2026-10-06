<?php

namespace App\Filament\Resources\FinancialTransactions\Pages;

use App\Filament\Concerns\RedirectsToIndexAfterEdit;
use App\Filament\Resources\FinancialTransactions\FinancialTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditFinancialTransaction extends EditRecord
{
    use RedirectsToIndexAfterEdit;

    protected static string $resource = FinancialTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
