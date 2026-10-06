<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rilevazione presenza')
                    ->schema([
                        Select::make('class_room_id')
                            ->label('Classe')
                            ->placeholder('Seleziona la classe')
                            ->relationship('classRoom', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if (! $state) {
                                    $set('students', []);

                                    return;
                                }

                                $students = Student::query()
                                    ->where('class_room_id', $state)
                                    ->orderBy('last_name')
                                    ->orderBy('first_name')
                                    ->get();

                                $set(
                                    'students',
                                    $students
                                        ->map(fn (Student $student) => [
                                            'student_id' => $student->id,
                                            'student_name' => "{$student->last_name} {$student->first_name}",
                                            'status' => 'present',
                                            'notes' => null,
                                        ])
                                        ->values()
                                        ->toArray()
                                );
                            }),

                        DatePicker::make('attendance_date')
                            ->label('Data')
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required(),

                        Repeater::make('students')
                            ->label('Studenti')
                            ->schema([
                                Hidden::make('student_id'),

                                TextInput::make('student_name')
                                    ->label('Studente')
                                    ->disabled()
                                    ->dehydrated(false),

                                Select::make('status')
                                    ->label('Stato')
                                    ->options([
                                        'present' => 'Presente',
                                        'absent' => 'Assente',
                                        'late' => 'In ritardo',
                                    ])
                                    ->default('present')
                                    ->native(false)
                                    ->required(),

                                Textarea::make('notes')
                                    ->label('Note')
                                    ->placeholder('Eventuali informazioni...')
                                    ->rows(2),
                            ])
                            ->columns(3)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->visible(fn ($get) => filled($get('class_room_id')))
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
