<?php

namespace App\Filament\Widgets;

use App\Models\Grade;
use Filament\Widgets\ChartWidget;

class StudentGradeEvolutionChart extends ChartWidget
{
    protected ?string $heading = 'Evoluzione delle valutazioni';

    protected ?string $description = 'Andamento dei voti dello studente nel corso del tempo.';

    protected ?string $maxHeight = '400px';

    protected int|string|array $columnSpan = 'full';

    public ?int $studentId = null;

    protected function getData(): array
    {
        if (! $this->studentId) {
            return [
                'labels' => [],
                'datasets' => [
                    [
                        'label' => 'Voto',
                        'data' => [],
                    ],
                ],
            ];
        }

        $grades = Grade::query()
            ->where('student_id', $this->studentId)
            ->orderBy('grade_date')
            ->orderBy('id')
            ->get();

        return [
            'labels' => $grades
                ->map(fn ($grade) => $grade->grade_date->format('d/m/Y'))
                ->values()
                ->toArray(),

            'datasets' => [
                [
                    'label' => 'Voto',
                    'data' => $grades
                        ->map(fn ($grade) => round((float) $grade->grade, 1))
                        ->values()
                        ->toArray(),
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {
                            return 'Voto: ' + context.parsed.y.toFixed(1);
                        }",
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Data',
                    ],
                ],
                'y' => [
                    'min' => 0,
                    'max' => 10,
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 2,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Voto',
                    ],
                ],
            ],
            'elements' => [
                'line' => [
                    'tension' => 0.35,
                ],
                'point' => [
                    'radius' => 4,
                    'hoverRadius' => 6,
                ],
            ],
        ];
    }
}
