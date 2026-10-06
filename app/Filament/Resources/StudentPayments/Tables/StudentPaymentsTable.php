<?php

namespace App\Filament\Resources\StudentPayments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentPaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.first_name')
                    ->label('Studente')
                    ->formatStateUsing(
                        fn ($state, $record): string => $record->student
                                ? "{$record->student->first_name} {$record->student->last_name}"
                                : 'N/D'
                    )
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->icon('heroicon-o-user'),

                TextColumn::make('installment_number')
                    ->label('Rata')
                    ->numeric()
                    ->sortable()
                    ->badge(),

                TextColumn::make('description')
                    ->label('Descrizione')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'Installment payment' => 'Pagamento rata',
                            'Tuition fee' => 'Quota scolastica',
                            'Registration fee' => 'Quota di iscrizione',
                            'Monthly payment' => 'Pagamento mensile',
                            default => $state ?? '-',
                        }
                    )
                    ->searchable()
                    ->limit(40),

                TextColumn::make('amount')
                    ->label('Importo')
                    ->money('EUR')
                    ->sortable(),

                TextColumn::make('payment_date')
                    ->label('Data pagamento')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Metodo di pagamento')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'cash' => 'Contanti',
                            'bank_transfer' => 'Bonifico bancario',
                            'card' => 'Carta',
                            'other' => 'Altro',
                            default => $state,
                        }
                    ),

                TextColumn::make('receipt_number')
                    ->label('Numero ricevuta')
                    ->searchable()
                    ->placeholder('-'),

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
