<?php

namespace App\Filament\Widgets;

use App\Models\Grade;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentAcademicLevel extends StatsOverviewWidget
{
    protected ?int $studentId = null;

    protected function getStats(): array
    {
        if (! $this->studentId) {
            return [];
        }

        $grades = Grade::query()
            ->where('student_id', $this->studentId)
            ->orderBy('grade_date')
            ->orderBy('id')
            ->pluck('grade');

        if ($grades->isEmpty()) {
            return [
                Stat::make('Livello', '—')
                    ->description('Nessuna valutazione disponibile')
                    ->icon('heroicon-o-academic-cap'),

                Stat::make('Tendenza', '—')
                    ->description('Dati insufficienti')
                    ->icon('heroicon-o-minus'),
            ];
        }

        $average = (float) $grades->avg();

        $level = match (true) {
            $average >= 9.0 => 'Eccellente',
            $average >= 8.0 => 'Molto buono',
            $average >= 7.0 => 'Buono',
            $average >= 6.0 => 'Sufficiente',
            $average >= 5.0 => 'Da migliorare',
            default => 'Insufficiente',
        };

        if ($grades->count() < 2) {
            $trend = 'Stabile';
            $trendDescription = 'Una sola valutazione disponibile';
            $trendIcon = 'heroicon-o-minus';
        } else {
            $middle = max(1, (int) floor($grades->count() / 2));

            $previousGrades = $grades->take($middle);
            $recentGrades = $grades->slice($middle);

            $previousAverage = (float) $previousGrades->avg();
            $recentAverage = (float) $recentGrades->avg();

            $difference = $recentAverage - $previousAverage;

            if ($difference > 0.3) {
                $trend = 'Positiva';
                $trendDescription = 'Lo studente è in miglioramento';
                $trendIcon = 'heroicon-o-arrow-trending-up';
            } elseif ($difference < -0.3) {
                $trend = 'Negativa';
                $trendDescription = 'Lo studente è in calo';
                $trendIcon = 'heroicon-o-arrow-trending-down';
            } else {
                $trend = 'Stabile';
                $trendDescription = 'Rendimento relativamente stabile';
                $trendIcon = 'heroicon-o-minus';
            }
        }

        return [
            Stat::make('Livello', $level)
                ->description('Valutazione generale dello studente')
                ->icon('heroicon-o-academic-cap'),

            Stat::make('Tendenza', $trend)
                ->description($trendDescription)
                ->icon($trendIcon),
        ];
    }
}
