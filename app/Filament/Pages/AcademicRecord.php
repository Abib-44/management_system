<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Resources\Pages\ViewRecord;

class AcademicRecord extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected string $view = 'filament.pages.academic-record';

    public function getTitle(): string
    {
        return 'Scheda valutativa';
    }

    protected function getViewData(): array
    {
        $student = $this->record->load([
            'classRoom',
            'grades.subject',
            'grades.lesson',
        ]);

        $grades = $student->grades
            ->sortByDesc('grade_date')
            ->values();

        $subjects = $grades
            ->groupBy(
                fn ($grade) => $grade->subject?->name ?? 'Materia non specificata'
            )
            ->map(function ($subjectGrades) {
                return [
                    'average' => round(
                        $subjectGrades->avg(
                            fn ($grade) => (float) $grade->grade
                        ),
                        2
                    ),
                    'count' => $subjectGrades->count(),
                    'grades' => $subjectGrades,
                ];
            })
            ->sortKeys();

        return [
            'student' => $student,
            'grades' => $grades,
            'subjects' => $subjects,

            'generalAverage' => $grades->isNotEmpty()
                ? round(
                    $grades->avg(
                        fn ($grade) => (float) $grade->grade
                    ),
                    2
                )
                : null,

            'totalGrades' => $grades->count(),

            'subjectCount' => $subjects->count(),

            'highestGrade' => $grades->isNotEmpty()
                ? $grades->max(
                    fn ($grade) => (float) $grade->grade
                )
                : null,

            'lowestGrade' => $grades->isNotEmpty()
                ? $grades->min(
                    fn ($grade) => (float) $grade->grade
                )
                : null,
        ];
    }
}
