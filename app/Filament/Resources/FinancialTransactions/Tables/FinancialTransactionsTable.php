<?php

namespace App\Filament\Resources\FinancialTransactions\Tables;

use App\Filament\Resources\FinancialTransactions\FinancialTransactionResource;
use App\Filament\Resources\FinancialTransactions\Schemas\FinancialTransactionForm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FinancialTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'income' => 'Entrata',
                            'expense' => 'Uscita',
                            default => $state,
                        }
                    )
                    ->color(
                        fn (string $state): string => match ($state) {
                            'income' => 'success',
                            'expense' => 'danger',
                            default => 'gray',
                        }
                    )
                    ->icon(
                        fn (string $state): string => match ($state) {
                            'income' => 'heroicon-o-arrow-trending-up',
                            'expense' => 'heroicon-o-arrow-trending-down',
                            default => 'heroicon-o-minus',
                        }
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('category.name')
                    ->label('Categoria')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-tag'),

                TextColumn::make('description')
                    ->label('Descrizione')
                    ->searchable()
                    ->limit(45)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('amount')
                    ->label('Importo')
                    ->formatStateUsing(function ($state, $record): string {
                        $amount = abs((float) $state);

                        $formatted = number_format(
                            $amount,
                            2,
                            ',',
                            '.'
                        );

                        return match ($record->type) {
                            'income' => '+'.$formatted.' €',
                            'expense' => '-'.$formatted.' €',
                            default => $formatted.' €',
                        };
                    })
                    ->color(
                        fn ($record): string => match ($record->type) {
                            'income' => 'success',
                            'expense' => 'danger',
                            default => 'gray',
                        }
                    )
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Metodo di pagamento')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): ?string => FinancialTransactionForm::PAYMENT_METHODS[$state] ?? $state
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'cash' => 'success',
                            'bank_transfer' => 'info',
                            'card', 'pos' => 'warning',
                            default => 'gray',
                        }
                    ),

                TextColumn::make('scope')
                    ->label('Ambito')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn (): bool => ! auth()->user()->isSchoolOnly())
                    ->formatStateUsing(
                        fn (?string $state): ?string => FinancialTransactionForm::SCOPES[$state] ?? $state
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'school' => 'info',
                            'association' => 'warning',
                            default => 'gray',
                        }
                    ),

                TextColumn::make('receipt_number')
                    ->label('Numero ricevuta')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('createdBy.name')
                    ->label('Inserito da')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->placeholder('-')
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
                Filter::make('transaction_date')
                    ->label('Data')
                    ->form([
                        DatePicker::make('date')
                            ->label('Giorno'),
                    ])
                    ->query(
                        function (Builder $query, array $data): Builder {
                            return $query->when(
                                $data['date'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate(
                                    'transaction_date',
                                    $date
                                )
                            );
                        }
                    ),

                Filter::make('date_range')
                    ->label('Periodo')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dal'),

                        DatePicker::make('until')
                            ->label('Al'),
                    ])
                    ->query(
                        function (Builder $query, array $data): Builder {
                            return $query
                                ->when(
                                    $data['from'] ?? null,
                                    fn (Builder $query, $date): Builder => $query->whereDate(
                                        'transaction_date',
                                        '>=',
                                        $date
                                    )
                                )
                                ->when(
                                    $data['until'] ?? null,
                                    fn (Builder $query, $date): Builder => $query->whereDate(
                                        'transaction_date',
                                        '<=',
                                        $date
                                    )
                                );
                        }
                    ),

                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'income' => 'Entrate',
                        'expense' => 'Uscite',
                    ]),

                SelectFilter::make('scope')
                    ->label('Ambito')
                    ->options(FinancialTransactionForm::SCOPES)
                    ->visible(fn (): bool => ! auth()->user()->isSchoolOnly()),

                SelectFilter::make('category_id')
                    ->label('Categoria')
                    ->relationship(
                        'category',
                        'name',
                        fn (Builder $query) => $query->visibleTo(auth()->user())
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('payment_method')
                    ->label('Metodo di pagamento')
                    ->options(FinancialTransactionForm::PAYMENT_METHODS),
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
                fn ($record): string => FinancialTransactionResource::getUrl(
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