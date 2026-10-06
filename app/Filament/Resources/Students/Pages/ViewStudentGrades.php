<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Filament\Widgets\StudentAcademicLevel;
use App\Filament\Widgets\StudentAcademicStats;
use App\Filament\Widgets\StudentGradeEvolutionChart;
use App\Filament\Widgets\StudentSubjectAverageChart;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewStudentGrades extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected static ?string $title = 'Valutazioni';

    public function getHeaderWidgets(): array
    {
        return [
            StudentAcademicStats::make([
                'studentId' => $this->record->id,
            ]),

            StudentSubjectAverageChart::make([
                'studentId' => $this->record->id,
            ]),

            StudentGradeEvolutionChart::make([
                'studentId' => $this->record->id,
            ]),

            StudentAcademicLevel::make([
                'studentId' => $this->record->id,
            ]),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                // niente form/infolist e niente relation managers:
                // i widget dell'header saranno l'unico contenuto della pagina
            ]);
    }
}
