<?php

namespace App\Filament\Resources\Members\Tables;

use App\Filament\Resources\Members\MemberResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('last_name')
                    ->label('Cognome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('first_name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Telefono')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->searchable(),

                TextColumn::make('assembly_status')
                    ->label('Stato assemblea')
                    ->searchable(),

                TextColumn::make('renewal_date')
                    ->label('Data di rinnovo')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('annual_fee')
                    ->label('Quota annuale')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('registration_date')
                    ->label('Data di iscrizione')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                //
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
                fn ($record): string => MemberResource::getUrl(
                    'view',
                    ['record' => $record]
                )
            )

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Elimina selezionati'),
                ]),
            ]);
    }
}
