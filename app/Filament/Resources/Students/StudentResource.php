<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\AcademicRecord;
use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Pages\ViewStudent;
use App\Filament\Resources\Students\Pages\ViewStudentAttendances;
use App\Filament\Resources\Students\Pages\ViewStudentDocuments;
use App\Filament\Resources\Students\Pages\ViewStudentGrades;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Studenti';

    protected static ?string $modelLabel = 'Studente';

    protected static ?string $pluralModelLabel = 'Studenti';

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table)
            ->modifyQueryUsing(
                fn (Builder $query) => $query
                    ->withCount('grades')
                    ->withAvg('grades', 'grade')
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'view' => ViewStudent::route('/{record}'),
            'edit' => EditStudent::route('/{record}/edit'),
            'grades' => ViewStudentGrades::route('/{record}/grades'),
            'academic-record' => AcademicRecord::route('/{record}/academic-record'),
            'attendances' => ViewStudentAttendances::route('/{record}/attendances'),
            'documents' => ViewStudentDocuments::route('/{record}/documents'),
        ];
    }
}
