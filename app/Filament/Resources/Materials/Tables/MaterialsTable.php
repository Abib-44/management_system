<?php

namespace App\Filament\Resources\Materials\Tables;

use App\Filament\Resources\Materials\MaterialResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MaterialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Materiale')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('subject.name')
                    ->label('Materia')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->placeholder('Non specificato'),

                TextColumn::make('quantity')
                    ->label('Quantità')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('available_quantity')
                    ->label('Disponibili')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('location')
                    ->label('Posizione')
                    ->searchable()
                    ->placeholder('Non specificata')
                    ->toggleable(),

                IconColumn::make('active')
                    ->label('Attivo')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Modificato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                SelectFilter::make('subject_id')
                    ->label('Materia')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('active')
                    ->label('Stato')
                    ->placeholder('Tutti')
                    ->trueLabel('Attivi')
                    ->falseLabel('Non attivi'),
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
                fn ($record): string => MaterialResource::getUrl(
                    'view',
                    ['record' => $record]
                )
            )

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Elimina selezionati'),
                ]),
            ])

            ->defaultSort('name');
    }
}
