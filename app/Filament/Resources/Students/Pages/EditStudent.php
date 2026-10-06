<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Concerns\RedirectsToIndexAfterEdit;
use App\Filament\Resources\Students\StudentResource;
use App\Filament\Widgets\AttendanceCalendarWidget;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStudent extends EditRecord
{
    use RedirectsToIndexAfterEdit;

    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            AttendanceCalendarWidget::class,
        ];
    }
}
