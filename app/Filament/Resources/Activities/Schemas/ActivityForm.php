<?php

namespace App\Filament\Resources\Activities\Schemas;

use App\Models\Activity;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dettagli attività')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label('Titolo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        DatePicker::make('activity_date')
                            ->label('Data')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        TextInput::make('location')
                            ->label('Luogo')
                            ->required()
                            ->maxLength(255),
                        Select::make('status')
                            ->label('Stato')
                            ->options(Activity::STATUS_LABELS)
                            ->default('scheduled')
                            ->required()
                            ->native(false),
                        TextInput::make('responsible_name')
                            ->label('Responsabile')
                            ->maxLength(255)
                            ->datalist(fn (): array => Activity::responsibleOptions()),
                        Textarea::make('notes')
                            ->label('Note')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
