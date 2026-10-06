<?php

namespace App\Filament\Resources\FinancialCategories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FinancialCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Categoria finanziaria')
                    ->description('Informazioni principali della categoria.')
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nome')
                            ->weight('bold')
                            ->icon('heroicon-o-tag'),

                        TextEntry::make('type')
                            ->label('Tipo')
                            ->badge()
                            ->formatStateUsing(
                                fn (string $state): string => match ($state) {
                                    'income' => 'Entrata',
                                    'expense' => 'Uscita',
                                    default => $state,
                                }
                            ),

                        TextEntry::make('area')
                            ->label('Area')
                            ->badge()
                            ->formatStateUsing(
                                fn (string $state): string => match ($state) {
                                    'all' => 'Generale',
                                    'treasury' => 'Tesoreria',
                                    'school' => 'Scuola',
                                    default => $state,
                                }
                            ),
                    ]),

                Section::make('Informazioni di sistema')
                    ->description('Informazioni sulla creazione e modifica della categoria.')
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Creata il')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Ultima modifica')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
