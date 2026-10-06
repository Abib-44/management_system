<?php

namespace App\Filament\Resources\MembershipFees\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MembershipFeeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni quota associativa')
                    ->description('Dettagli della quota associativa del socio')
                    ->icon('heroicon-o-banknotes')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('member.first_name')
                            ->label('Socio')
                            ->formatStateUsing(
                                fn ($state, $record) => $record->member
                                        ? $record->member->first_name.' '.$record->member->last_name
                                        : 'N/D'
                            )
                            ->weight('bold')
                            ->icon('heroicon-o-user'),

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
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('')
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
