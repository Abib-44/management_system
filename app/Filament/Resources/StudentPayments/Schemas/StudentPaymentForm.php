<?php

namespace App\Filament\Resources\StudentPayments\Schemas;

use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentPaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pagamento studente')
                    ->description('Inserisci i dati principali del pagamento.')
                    ->icon('heroicon-o-banknotes')
                    ->columns(2)
                    ->schema([
                        Select::make('student_id')
                            ->label('Studente')
                            ->relationship('student', 'first_name')
                            ->getOptionLabelFromRecordUsing(
                                fn (Student $record): string => "{$record->first_name} {$record->last_name}"
                            )
                            ->searchable(['first_name', 'last_name'])
                            ->preload()
                            ->required()
                            ->native(false)
                            ->placeholder('Seleziona lo studente')
                            ->prefixIcon('heroicon-o-user')
                            ->columnSpanFull(),

                        TextInput::make('installment_number')
                            ->label('Numero rata')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Es. 1')
                            ->prefixIcon('heroicon-o-hashtag'),

                        TextInput::make('amount')
                            ->label('Importo')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->inputMode('decimal')
                            ->placeholder('0,00')
                            ->prefixIcon('heroicon-o-currency-euro'),

                        TextInput::make('description')
                            ->label('Descrizione')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Es. Pagamento rata')
                            ->columnSpanFull(),
                    ]),

                Section::make('Dettagli pagamento')
                    ->description('Data, metodo e riferimento del pagamento.')
                    ->icon('heroicon-o-credit-card')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('payment_date')
                            ->label('Data pagamento')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->prefixIcon('heroicon-o-calendar'),

                        Select::make('payment_method')
                            ->label('Metodo di pagamento')
                            ->options([
                                'cash' => 'Contanti',
                                'bank_transfer' => 'Bonifico bancario',
                                'card' => 'Carta',
                                'check' => 'Assegno',
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
                            ->prefixIcon('heroicon-o-document-text')
                            ->columnSpanFull(),
                    ]),

                Section::make('Note')
                    ->description('Eventuali annotazioni relative al pagamento.')
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
