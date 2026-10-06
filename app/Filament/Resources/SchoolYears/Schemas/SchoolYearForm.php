<?php

namespace App\Filament\Resources\SchoolYears\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SchoolYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make()
                    ->schema([

                        // ANNO SCOLASTICO

                        TextInput::make('name')
                            ->label('Anno scolastico')
                            ->placeholder('Es. 2025/2026')
                            ->prefixIcon('heroicon-o-academic-cap')
                            ->required()
                            ->maxLength(20),

                        // DATA DI INIZIO

                        DatePicker::make('start_date')
                            ->label('Data di inizio')
                            ->placeholder('Data di inizio')
                            ->prefixIcon('heroicon-o-calendar')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required(),

                        // DATA DI FINE

                        DatePicker::make('end_date')
                            ->label('Data di fine')
                            ->placeholder('Data di fine')
                            ->prefixIcon('heroicon-o-calendar')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required(),

                        // STATO

                        Toggle::make('is_active')
                            ->label('Anno scolastico attivo')
                            ->helperText('Indica se questo è l\'anno scolastico attualmente in corso.')
                            ->default(false)
                            ->onColor('success')
                            ->offColor('gray')
                            ->inline(false),

                    ])
                    ->columns(3)
                    ->columnSpanFull(),

            ]);
    }
}
