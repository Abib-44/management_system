<?php

namespace App\Filament\Resources\Lessons\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make()
                    ->schema([

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

                        // MATERIA

                        Select::make('subject_id')
                            ->label('Materia')
                            ->placeholder('Seleziona la materia')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-o-academic-cap')
                            ->native(false)
                            ->required(),

                        // DATA

                        DatePicker::make('lesson_date')
                            ->label('Data della lezione')
                            ->placeholder('Data della lezione')
                            ->prefixIcon('heroicon-o-calendar')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required(),

                        // INSEGNANTE

                        TextInput::make('teacher')
                            ->label('Insegnante')
                            ->placeholder('Nome e cognome')
                            ->prefixIcon('heroicon-o-user')
                            ->maxLength(150),

                        // ARGOMENTI

                        Textarea::make('topics')
                            ->label('Argomenti')
                            ->placeholder('Inserisci gli argomenti trattati durante la lezione...')
                            ->rows(5)
                            ->columnSpan(2),

                    ])
                    ->columns(3)
                    ->columnSpanFull(),

            ]);
    }
}
