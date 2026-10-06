<?php

namespace App\Filament\Resources\Grades\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GradeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make()
                    ->schema([

                        // STUDENTE

                        Select::make('student_id')
                            ->label('Studente')
                            ->placeholder('Seleziona lo studente')
                            ->relationship('student', 'last_name')
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-o-user')
                            ->native(false)
                            ->required(),

                        // CLASSE

                        Select::make('class_room_id')
                            ->label('Classe')
                            ->placeholder('Seleziona la classe')
                            ->relationship('classRoom', 'name')
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-o-building-office-2')
                            ->native(false)
                            ->required(),

                        // LEZIONE

                        Select::make('lesson_id')
                            ->label('Lezione')
                            ->placeholder('Seleziona la lezione')
                            ->relationship('lesson', 'id')
                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                return $record->subject->name
                                    .' — '
                                    .$record->lesson_date->format('d/m/Y');
                            })
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-o-book-open')
                            ->native(false),

                        // DATA DEL VOTO

                        DatePicker::make('grade_date')
                            ->label('Data del voto')
                            ->placeholder('Data del voto')
                            ->prefixIcon('heroicon-o-calendar')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required(),

                        // MATERIA

                        TextInput::make('subject')
                            ->label('Materia')
                            ->placeholder('Es. Matematica')
                            ->prefixIcon('heroicon-o-academic-cap')
                            ->required()
                            ->maxLength(255),

                        // VOTO

                        TextInput::make('grade')
                            ->label('Voto')
                            ->placeholder('Es. 8')
                            ->prefixIcon('heroicon-o-star')
                            ->numeric()
                            ->required(),

                        // NOTE

                        Textarea::make('notes')
                            ->label('Note')
                            ->placeholder('Eventuali informazioni aggiuntive...')
                            ->rows(4)
                            ->columnSpanFull(),

                    ])
                    ->columns(3)
                    ->columnSpanFull(),

            ]);
    }
}
