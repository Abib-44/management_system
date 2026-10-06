<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Concerns\RedirectsToIndexAfterEdit;
use App\Filament\Resources\Attendances\AttendanceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAttendance extends EditRecord
{
    use RedirectsToIndexAfterEdit;

    protected static string $resource = AttendanceResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['student_ids'] = [
            $this->record->student_id,
        ];

        return $data;
    }

    protected function handleRecordUpdate(
        Model $record,
        array $data
    ): Model {
        $studentIds = $data['student_ids'] ?? [];

        unset($data['student_ids']);

        $studentId = $studentIds[0] ?? $record->student_id;

        $record->update([
            'student_id' => $studentId,
            'class_room_id' => $data['class_room_id'],
            'attendance_date' => $data['attendance_date'],
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),

            DeleteAction::make(),
        ];
    }
}
