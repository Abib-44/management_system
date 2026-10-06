<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Resources\Attendances\AttendanceResource;
use App\Models\Attendance;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $students = $data['students'] ?? [];

        unset($data['students']);

        return DB::transaction(function () use ($data, $students) {
            $firstAttendance = null;

            foreach ($students as $student) {
                $attendance = Attendance::updateOrCreate(
                    [
                        'student_id' => $student['student_id'],
                        'class_room_id' => $data['class_room_id'],
                        'attendance_date' => $data['attendance_date'],
                    ],
                    [
                        'status' => $student['status'],
                        'notes' => $student['notes'] ?? null,
                    ]
                );

                $firstAttendance ??= $attendance;
            }

            return $firstAttendance;
        });
    }
}
