<?php

namespace App\Filament\Resources\SchoolYears\Tables;

use App\Filament\Resources\SchoolYears\SchoolYearResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchoolYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Anno scolastico')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('start_date')
                    ->label('Data di inizio')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Data di fine')
                    ->date('d/m/Y')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean()
                    ->sortable(),
            ])

            ->recordActions([
                EditAction::make()
                    ->label(false)
                    ->icon('heroicon-o-pencil')
                    ->tooltip('Modifica'),

                DeleteAction::make()
                    ->label(false)
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->tooltip('Elimina'),
            ])

            ->recordUrl(
                fn ($record): string => SchoolYearResource::getUrl(
                    'view',
                    ['record' => $record]
                )
            )

            ->defaultSort('start_date', 'desc');
    }
}
