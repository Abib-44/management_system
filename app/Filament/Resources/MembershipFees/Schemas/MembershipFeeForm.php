<?php

namespace App\Filament\Resources\MembershipFees\Schemas;

use App\Models\Member;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MembershipFeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Quota associativa')
                    ->description('Inserisci i dati principali della quota del socio.')
                    ->icon('heroicon-o-banknotes')
                    ->columns(2)
                    ->schema([
                        Select::make('member_id')
                            ->label('Socio')
                            ->relationship('member', 'first_name')
                            ->getOptionLabelFromRecordUsing(
                                fn (Member $record): string => "{$record->first_name} {$record->last_name}"
                            )
                            ->searchable(['first_name', 'last_name'])
                            ->preload()
                            ->required()
                            ->native(false)
                            ->placeholder('Seleziona il socio')
                            ->prefixIcon('heroicon-o-user')
                            ->columnSpanFull(),

                        TextInput::make('amount')
                            ->label('Importo')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->placeholder('0,00')
                            ->prefixIcon('heroicon-o-currency-euro'),

                        DatePicker::make('payment_date')
                            ->label('Data pagamento')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->prefixIcon('heroicon-o-calendar'),
                    ]),

                Section::make('Pagamento')
                    ->description('Modalità di pagamento e riferimento della ricevuta.')
                    ->icon('heroicon-o-credit-card')
                    ->columns(2)
                    ->schema([
                        Select::make('payment_method')
                            ->label('Metodo di pagamento')
                            ->options([
                                'cash' => 'Contanti',
                                'bank_transfer' => 'Bonifico bancario',
                                'card' => 'Carta',
                                'other' => 'Altro',
                            ])
                            ->default('cash')
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-o-wallet'),

                        TextInput::make('receipt_number')
                            ->label('Numero ricevuta')
                            ->maxLength(50)
                            ->placeholder('Es. RIC-2026-001')
                            ->prefixIcon('heroicon-o-document-text'),
                    ]),

                Section::make('Note')
                    ->description('Aggiungi eventuali informazioni aggiuntive.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Note')
                            ->placeholder('Inserisci eventuali note...')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
