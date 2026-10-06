<?php

namespace App\Filament\Resources\Materials\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni del materiale')
                    ->schema([
                        Select::make('subject_id')
                            ->label('Materia')
                            ->placeholder('Seleziona la materia')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-o-book-open')
                            ->native(false)
                            ->required(),

                        TextInput::make('name')
                            ->label('Nome')
                            ->placeholder('Inserisci il nome del materiale')
                            ->prefixIcon('heroicon-o-cube')
                            ->required()
                            ->maxLength(255),

                        Select::make('type')
                            ->label('Tipo')
                            ->placeholder('Seleziona il tipo')
                            ->options([
                                'Libro' => 'Libro',
                                'Attrezzatura' => 'Attrezzatura',
                                'Computer' => 'Computer',
                                'Strumento' => 'Strumento',
                                'Cancelleria' => 'Cancelleria',
                                'Altro' => 'Altro',
                            ])
                            ->searchable()
                            ->native(false)
                            ->prefixIcon('heroicon-o-tag')
                            ->live(),

                        TextInput::make('custom_type')
                            ->label('Specifica il tipo')
                            ->placeholder('Es. Proiettore, Microscopio, Tablet...')
                            ->prefixIcon('heroicon-o-pencil')
                            ->maxLength(255)
                            ->visible(fn ($get) => $get('type') === 'Altro')
                            ->required(fn ($get) => $get('type') === 'Altro')
                            ->dehydrated(false),

                        TextInput::make('quantity')
                            ->label('Quantità')
                            ->placeholder('Inserisci la quantità totale')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefixIcon('heroicon-o-archive-box')
                            ->required(),

                        TextInput::make('available_quantity')
                            ->label('Quantità disponibile')
                            ->placeholder('Inserisci la quantità disponibile')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefixIcon('heroicon-o-check-circle')
                            ->required()
                            ->lte('quantity'),

                        TextInput::make('location')
                            ->label('Posizione')
                            ->placeholder('Es. Aula 101, Laboratorio di fisica...')
                            ->prefixIcon('heroicon-o-map-pin')
                            ->maxLength(255),

                        Toggle::make('active')
                            ->label('Materiale attivo')
                            ->helperText(
                                'Se disattivato, il materiale non sarà più utilizzato normalmente.'
                            )
                            ->default(true)
                            ->inline(false),

                        Textarea::make('description')
                            ->label('Descrizione')
                            ->placeholder('Inserisci una descrizione del materiale...')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
