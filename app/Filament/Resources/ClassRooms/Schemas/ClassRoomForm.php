<?php

namespace App\Filament\Resources\ClassRooms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClassRoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make()
                    ->schema([

                        // CLASSE

                        TextInput::make('name')
                            ->label('Nome classe')
                            ->placeholder('Es. 1A, 2B, 3C...')
                            ->prefixIcon('heroicon-o-building-office-2')
                            ->required()
                            ->maxLength(255),

                        Select::make('school_year_id')
                            ->label('Anno scolastico')
                            ->placeholder('Seleziona l\'anno scolastico')
                            ->relationship('schoolYear', 'name')
                            ->prefixIcon('heroicon-o-calendar')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),

                        TextInput::make('responsible')
                            ->label('Responsabile')
                            ->placeholder('Nome e cognome')
                            ->prefixIcon('heroicon-o-user')
                            ->maxLength(255),

                    ])
                    ->columns(3)
                    ->columnSpanFull(),

            ]);
    }
}
