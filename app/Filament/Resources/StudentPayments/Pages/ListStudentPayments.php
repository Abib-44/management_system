<?php

namespace App\Filament\Resources\StudentPayments\Pages;

use App\Filament\Resources\StudentPayments\StudentPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudentPayments extends ListRecords
{
    protected static string $resource = StudentPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
