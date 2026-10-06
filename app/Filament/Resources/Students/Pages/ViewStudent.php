<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Filament\Widgets\AttendanceCalendarWidget;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('academicRecord')
                ->label('Scheda valutativa')
                ->icon('heroicon-o-academic-cap')
                ->url(fn () => StudentResource::getUrl(
                    'academic-record',
                    ['record' => $this->record]
                )),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            AttendanceCalendarWidget::class,
        ];
    }

    protected string $view = 'filament.Resources.Students.student-profile';
}
