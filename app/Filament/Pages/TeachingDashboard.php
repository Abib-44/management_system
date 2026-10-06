<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\Lesson;
use App\Models\SchoolYear;
use App\Models\Student;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class TeachingDashboard extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.teaching-dashboard';

    public function getStudentsCount(): int
    {
        return Student::count();
    }

    public function getClassesCount(): int
    {
        return ClassRoom::count();
    }

    public function getActiveSchoolYearsCount(): int
    {
        return SchoolYear::where('is_active', true)->count();
    }

    public function getLessonsThisMonth(): int
    {
        return Lesson::whereBetween('lesson_date', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ])->count();
    }

    public function getGradesThisMonth(): int
    {
        return Grade::whereBetween('grade_date', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ])->count();
    }

    public function getAverageGrade(): ?float
    {
        $average = Grade::avg('grade');

        return $average !== null ? round((float) $average, 2) : null;
    }

    public function getAttendanceThisMonth(): int
    {
        return Attendance::whereBetween('attendance_date', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ])->count();
    }
}
