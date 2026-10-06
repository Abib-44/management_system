<?php

namespace App\Filament\Resources\FinancialCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FinancialCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Categoria finanziaria')
                    ->description('Configura il nome, il tipo e l’area della categoria.')
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome categoria')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Es. Quote associative')
                            ->prefixIcon('heroicon-o-tag')
                            ->columnSpanFull(),

                        Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'income' => 'Entrata',
                                'expense' => 'Uscita',
                            ])
                            ->required()
                            ->native(false)
                            ->placeholder('Seleziona il tipo')
                            ->prefixIcon('heroicon-o-arrows-right-left'),

                        Select::make('area')
                            ->label('Area')
                            ->options([
                                'association' => 'Associazione',
                                'social' => 'Sociale',
                                'management' => 'Gestione',
                                'general' => 'Generale',
                            ])
                            ->default('general')
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-o-building-office'),
                    ]),
            ]);
    }
}
