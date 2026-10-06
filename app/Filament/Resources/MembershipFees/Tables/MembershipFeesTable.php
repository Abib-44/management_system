<?php

namespace App\Filament\Resources\MembershipFees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MembershipFeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member.first_name')
                    ->label('Socio')
                    ->formatStateUsing(
                        fn ($state, $record) => $record->member
                                ? $record->member->first_name.' '.$record->member->last_name
                                : 'N/D'
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Importo')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('payment_date')
                    ->label('Data pagamento')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Metodo di pagamento')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Contanti',
                        'bank_transfer' => 'Bonifico bancario',
                        'card' => 'Carta',
                        'other' => 'Altro',
                        default => $state,
                    }),

                TextColumn::make('receipt_number')
                    ->label('Numero ricevuta')
                    ->searchable(),

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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Elimina selezionati'),
                ]),
            ]);
    }
}
