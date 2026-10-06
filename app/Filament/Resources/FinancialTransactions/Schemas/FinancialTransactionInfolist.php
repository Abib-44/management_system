<?php

namespace App\Filament\Resources\FinancialTransactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FinancialTransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                /*
                 * RIEPILOGO
                 * Card principale nello stile della schermata mostrata:
                 * informazioni essenziali, tipo movimento e importo ben evidenziati.
                 */
                Section::make('Movimento finanziario')
                    ->icon('heroicon-o-banknotes')
                    ->iconColor('primary')
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->schema([
                        TextEntry::make('transaction_date')
                            ->label('Data')
                            ->date('d/m/Y')
                            ->icon('heroicon-o-calendar-days')
                            ->iconColor('gray'),

                        TextEntry::make('type')
                            ->label('Tipo movimento')
                            ->badge()
                            ->formatStateUsing(
                                fn (?string $state): string => match ($state) {
                                    'income' => 'Entrata',
                                    'expense' => 'Uscita',
                                    default => $state ?? '-',
                                }
                            )
                            ->color(
                                fn (?string $state): string => match ($state) {
                                    'income' => 'success',
                                    'expense' => 'danger',
                                    default => 'gray',
                                }
                            )
                            ->icon(
                                fn (?string $state): string => match ($state) {
                                    'income' => 'heroicon-o-arrow-trending-up',
                                    'expense' => 'heroicon-o-arrow-trending-down',
                                    default => 'heroicon-o-minus',
                                }
                            ),

                        TextEntry::make('amount')
                            ->label('Importo')
                            ->formatStateUsing(function ($state, $record): string {
                                $amount = abs((float) $state);
                                $formatted = number_format($amount, 2, ',', '.');

                                return match ($record?->type) {
                                    'income' => '+'.$formatted.' €',
                                    'expense' => '-'.$formatted.' €',
                                    default => $formatted.' €',
                                };
                            })
                            ->weight('bold')
                            ->size('xl')
                            ->color(
                                fn ($record): string => match ($record?->type) {
                                    'income' => 'success',
                                    'expense' => 'danger',
                                    default => 'gray',
                                }
                            )
                            ->icon(
                                fn ($record): string => match ($record?->type) {
                                    'income' => 'heroicon-o-arrow-up-circle',
                                    'expense' => 'heroicon-o-arrow-down-circle',
                                    default => 'heroicon-o-currency-euro',
                                }
                            )
                            ->iconColor(
                                fn ($record): string => match ($record?->type) {
                                    'income' => 'success',
                                    'expense' => 'danger',
                                    default => 'gray',
                                }
                            ),

                        TextEntry::make('payment_method')
                            ->label('Metodo di pagamento')
                            ->badge()
                            ->formatStateUsing(
                                fn (?string $state): string => match ($state) {
                                    'cash' => 'Contanti',
                                    'bank_transfer' => 'Bonifico bancario',
                                    'card' => 'Carta',
                                    'check' => 'Assegno',
                                    'other' => 'Altro',
                                    default => $state ?? '-',
                                }
                            )
                            ->color(
                                fn (?string $state): string => match ($state) {
                                    'cash' => 'success',
                                    'bank_transfer' => 'info',
                                    'card' => 'warning',
                                    'check' => 'gray',
                                    'other' => 'gray',
                                    default => 'gray',
                                }
                            ),
                    ]),

                /*
                 * DETTAGLI
                 */
                Section::make('Dettagli')
                    ->icon('heroicon-o-document-text')
                    ->iconColor('primary')
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 2,
                    ])
                    ->schema([
                        TextEntry::make('category.name')
                            ->label('Categoria')
                            ->icon('heroicon-o-tag')
                            ->iconColor('primary')
                            ->placeholder('Non specificata'),

                        TextEntry::make('scope')
                            ->label('Ambito')
                            ->badge()
                            ->formatStateUsing(
                                fn (?string $state): string => match ($state) {
                                    'general' => 'Generale',
                                    'school' => 'Scuola',
                                    'association' => 'Associazione',
                                    default => $state ?? '-',
                                }
                            )
                            ->color(
                                fn (?string $state): string => match ($state) {
                                    'general' => 'gray',
                                    'school' => 'info',
                                    'association' => 'warning',
                                    default => 'gray',
                                }
                            ),

                        TextEntry::make('receipt_number')
                            ->label('Numero ricevuta')
                            ->placeholder('Non specificato')
                            ->icon('heroicon-o-receipt-percent')
                            ->iconColor('gray'),

                        TextEntry::make('description')
                            ->label('Descrizione')
                            ->placeholder('Nessuna descrizione')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->columnSpanFull(),
                    ]),

                /*
                 * REGISTRAZIONE
                 */
                Section::make('Informazioni di registrazione')
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray')
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->schema([
                        TextEntry::make('createdBy.name')
                            ->label('Inserito da')
                            ->placeholder('Non specificato')
                            ->icon('heroicon-o-user')
                            ->iconColor('primary'),

                        TextEntry::make('created_at')
                            ->label('Creato il')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-')
                            ->icon('heroicon-o-clock')
                            ->iconColor('gray'),

                        TextEntry::make('updated_at')
                            ->label('Ultimo aggiornamento')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-')
                            ->icon('heroicon-o-pencil-square')
                            ->iconColor('gray'),
                    ]),
            ]);
    }
}
