<?php

namespace App\Filament\Widgets;

use App\Models\Grade;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentAcademicStats extends StatsOverviewWidget
{
    public ?int $studentId = null;

    protected function getStats(): array
    {
        $query = Grade::query()
            ->where('student_id', $this->studentId);

        $count = $query->count();
        $average = $query->avg('grade');
        $maximum = $query->max('grade');
        $minimum = $query->min('grade');

        return [
            Stat::make(
                'Media',
                $count > 0
                    ? number_format((float) $average, 1).' / 10'
                    : '—'
            ),

            Stat::make(
                'Valutazioni',
                $count
            ),

            Stat::make(
                'Massimo',
                $count > 0
                    ? number_format((float) $maximum, 1)
                    : '—'
            ),

            Stat::make(
                'Minimo',
                $count > 0
                    ? number_format((float) $minimum, 1)
                    : '—'
            ),
        ];
    }
}
