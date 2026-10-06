<?php

namespace App\Filament\Pages;

use App\Models\ClassRoom;
use App\Models\Lesson;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Pages\Page;

class TeachingAgenda extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.teaching-agenda';

    protected static ?string $slug = 'agenda';

    /** Mese visualizzato, formato Y-m */
    public string $month;

    /** Stringa vuota = tutte le classi */
    public string $classRoomId = '';

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function getTitle(): string
    {
        return 'Agenda';
    }

    public function previousMonth(): void
    {
        $this->month = $this->currentMonth()->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->month = $this->currentMonth()->addMonth()->format('Y-m');
    }

    public function goToToday(): void
    {
        $this->month = now()->format('Y-m');
    }

    protected function currentMonth(): Carbon
    {
        return Carbon::createFromFormat('Y-m-d', $this->month.'-01')->startOfMonth();
    }

    protected function getViewData(): array
    {
        $start = $this->currentMonth();
        $end = $start->copy()->endOfMonth();

        $lessons = Lesson::query()
            ->with(['subject', 'classRoom'])
            ->when($this->classRoomId !== '', fn ($q) => $q->where('class_room_id', $this->classRoomId))
            ->whereBetween('lesson_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('lesson_date')
            ->orderBy('id')
            ->get();

        $lessonsByDay = $lessons->groupBy(
            fn ($lesson) => Carbon::parse($lesson->lesson_date)->toDateString()
        );

        $subjectSummary = $lessons
            ->groupBy(fn ($lesson) => $lesson->subject?->name ?? 'Materia non specificata')
            ->map->count()
            ->sortKeys();

        $days = collect();
        $cursor = $start->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $end->copy()->endOfWeek(Carbon::SUNDAY);

        while ($cursor->lte($gridEnd)) {
            $days->push($cursor->copy());
            $cursor->addDay();
        }

        return [
            'classRooms' => ClassRoom::orderBy('name')->pluck('name', 'id'),
            'showClass' => $this->classRoomId === '',
            'monthStart' => $start,
            'days' => $days,
            'lessonsByDay' => $lessonsByDay,
            'subjectSummary' => $subjectSummary,
            'totalLessons' => $lessons->count(),
        ];
    }
}
