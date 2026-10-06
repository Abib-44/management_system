<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class AttendanceCalendarWidget extends FullCalendarWidget
{
    protected static bool $isDiscovered = false;

    public function fetchEvents(array $fetchInfo): array
    {
        if (! $this->record) {
            return [];
        }

        $studentId = $this->record instanceof Model
            ? $this->record->getKey()
            : $this->record;

        $start = Carbon::parse($fetchInfo['start'])->toDateString();
        $end = Carbon::parse($fetchInfo['end'])->toDateString();

        return Attendance::query()
            ->with('classRoom')
            ->where('student_id', $studentId)
            ->whereBetween('attendance_date', [$start, $end])
            ->orderBy('attendance_date')
            ->get()
            ->map(function (Attendance $attendance) {
                $date = Carbon::parse(
                    $attendance->attendance_date
                )->toDateString();

                $title = match ($attendance->status) {
                    'present' => 'Presente',
                    'absent' => 'Assente',
                    'late' => 'In ritardo',
                    default => 'Rilevazione',
                };

                return EventData::make()
                    ->id('attendance-'.$attendance->id)
                    ->title($title)
                    ->start($date)
                    ->allDay()
                    ->extendedProps([
                        'status' => $attendance->status,
                        'date' => $date,
                        'class_room' => $attendance->classRoom?->name,
                        'notes' => $attendance->notes,
                    ]);
            })
            ->values()
            ->toArray();
    }
}
