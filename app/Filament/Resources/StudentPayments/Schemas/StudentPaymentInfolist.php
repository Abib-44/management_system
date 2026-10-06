<?php

namespace App\Filament\Resources\StudentPayments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentPaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni pagamento')
                    ->description('Dettagli del pagamento effettuato dallo studente.')
                    ->icon('heroicon-o-banknotes')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('student.first_name')
                            ->label('Studente')
                            ->formatStateUsing(
                                fn ($state, $record): string => $record->student
                                        ? "{$record->student->first_name} {$record->student->last_name}"
                                        : 'N/D'
                            )
                            ->weight('bold')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('installment_number')
                            ->label('Rata')
                            ->badge()
                            ->icon('heroicon-o-hashtag'),

                        TextEntry::make('description')
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
                            ->columnSpanFull(),

                        TextEntry::make('amount')
                            ->label('Importo')
                            ->money('EUR')
                            ->weight('bold')
                            ->icon('heroicon-o-currency-euro'),

                        TextEntry::make('payment_date')
                            ->label('Data pagamento')
                            ->date('d/m/Y')
                            ->icon('heroicon-o-calendar'),

                        TextEntry::make('payment_method')
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

                        TextEntry::make('receipt_number')
                            ->label('Numero ricevuta')
                            ->placeholder('Non specificato')
                            ->icon('heroicon-o-document-text'),
                    ]),

                Section::make('Note')
                    ->description('Annotazioni relative al pagamento.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Note')
                            ->placeholder('Nessuna nota inserita')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Informazioni di sistema')
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Creato il')
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
