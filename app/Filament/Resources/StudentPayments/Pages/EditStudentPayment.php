<?php

namespace App\Filament\Resources\StudentPayments\Pages;

use App\Filament\Concerns\RedirectsToIndexAfterEdit;
use App\Filament\Resources\StudentPayments\StudentPaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStudentPayment extends EditRecord
{
    use RedirectsToIndexAfterEdit;

    protected static string $resource = StudentPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
