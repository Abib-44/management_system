<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Utente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('occurred_at')
                    ->label('Data e ora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('action')
                    ->label('Azione')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Creato',
                        'updated' => 'Modificato',
                        'deleted' => 'Eliminato',
                        'login' => 'Accesso',
                        'logout' => 'Disconnessione',
                        default => ucfirst($state),
                    })
                    ->searchable(),

                TextColumn::make('ip_address')
                    ->label('Indirizzo IP')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Modificato il')
                    ->dateTime('d/m/Y H:i:s')
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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Elimina selezionati'),
                ]),
            ]);
    }
}
