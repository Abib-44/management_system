<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | CONTENITORE PRINCIPALE
            |--------------------------------------------------------------------------
            */

            Section::make()
                ->extraAttributes([
                    'class' => 'student-main-container',
                ])
                ->schema([

                    Grid::make([
                        'default' => 1,
                        'lg' => 2,
                    ])
                        ->schema([

                            /*
                            |--------------------------------------------------------------------------
                            | INFORMAZIONI PERSONALI
                            |--------------------------------------------------------------------------
                            */

                            Section::make('Informazioni personali')
                                ->description(
                                    'Dati anagrafici e informazioni principali dello studente.'
                                )
                                ->extraAttributes([
                                    'class' => 'border-0 shadow-none rounded-none border-b border-gray-700',
                                ])
                                ->columns(2)
                                ->schema([

                                    TextInput::make('first_name')
                                        ->label('Nome')
                                        ->placeholder('Nome')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('last_name')
                                        ->label('Cognome')
                                        ->placeholder('Cognome')
                                        ->required()
                                        ->maxLength(255),

                                    DatePicker::make('birth_date')
                                        ->label('Data di nascita')
                                        ->native(false)
                                        ->displayFormat('d/m/Y'),

                                    Select::make('class_room_id')
                                        ->label('Classe')
                                        ->relationship('classRoom', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->native(false),
                                ]),

                            /*
                            |--------------------------------------------------------------------------
                            | GENITORI E CONTATTI
                            |--------------------------------------------------------------------------
                            */

                            Section::make('Genitori e contatti')
                                ->description(
                                    'Informazioni sui genitori e principali recapiti dello studente.'
                                )
                                ->extraAttributes([
                                    'class' => 'border-0 shadow-none rounded-none border-b border-gray-700',
                                ])
                                ->columns(2)
                                ->schema([

                                    TextInput::make('father_name')
                                        ->label('Nome del padre')
                                        ->placeholder('Nome del padre')
                                        ->maxLength(255),

                                    TextInput::make('mother_name')
                                        ->label('Nome della madre')
                                        ->placeholder('Nome della madre')
                                        ->maxLength(255),

                                    TextInput::make('father_phone')
                                        ->label('Telefono padre')
                                        ->tel()
                                        ->maxLength(20),

                                    TextInput::make('mother_phone')
                                        ->label('Telefono madre')
                                        ->tel()
                                        ->maxLength(20),

                                    TextInput::make('email')
                                        ->label('Email')
                                        ->email()
                                        ->maxLength(255),

                                    TextInput::make('address')
                                        ->label('Indirizzo')
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                ]),

                            /*
                            |--------------------------------------------------------------------------
                            | SITUAZIONE FINANZIARIA
                            |--------------------------------------------------------------------------
                            */

                            Section::make('Situazione finanziaria')
                                ->description(
                                    'Gestione della quota totale e del piano di pagamento.'
                                )
                                ->extraAttributes([
                                    'class' => 'border-0 shadow-none rounded-none border-b border-gray-700',
                                ])
                                ->columns(2)
                                ->schema([

                                    TextInput::make('total_fee')
                                        ->label('Quota totale')
                                        ->placeholder('0,00')
                                        ->numeric()
                                        ->prefix('€')
                                        ->required()
                                        ->minValue(0)
                                        ->step(0.01),

                                    Select::make('installment_plan')
                                        ->label('Piano rate')
                                        ->options([
                                            'no_interest' => 'Nessuna rata',
                                            'installment_1' => '1 rata',
                                            'installment_2' => '2 rate',
                                        ])
                                        ->native(false),
                                ]),

                            /*
                            |--------------------------------------------------------------------------
                            | PAGAMENTI
                            |--------------------------------------------------------------------------
                            */

                            Section::make('Pagamenti')
                                ->description(
                                    'Registra gli importi versati dallo studente.'
                                )
                                ->extraAttributes([
                                    'class' => 'border-0 shadow-none rounded-none border-b border-gray-700',
                                ])
                                ->columnSpanFull()
                                ->schema([

                                    Repeater::make('payments')
                                        ->relationship()
                                        ->hiddenLabel()
                                        ->addActionLabel('Aggiungi pagamento')
                                        ->defaultItems(0)
                                        ->grid(3)
                                        ->itemLabel(
                                            fn (array $state): string => ! empty($state['amount'])
                                                    ? number_format(
                                                        (float) $state['amount'],
                                                        2,
                                                        ',',
                                                        '.'
                                                    ).' €'
                                                    : 'Nuovo pagamento'
                                        )
                                        ->collapsible()
                                        ->schema([

                                            DatePicker::make('payment_date')
                                                ->label('Data pagamento')
                                                ->default(now())
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->required(),

                                            TextInput::make('amount')
                                                ->label('Importo')
                                                ->placeholder('0,00')
                                                ->numeric()
                                                ->prefix('€')
                                                ->required()
                                                ->minValue(0.01)
                                                ->step(0.01),

                                            Select::make('payment_method')
                                                ->label('Metodo di pagamento')
                                                ->options([
                                                    'cash' => 'Contanti',
                                                    'bank_transfer' => 'Bonifico',
                                                    'card' => 'Carta',
                                                    'other' => 'Altro',
                                                ])
                                                ->native(false),

                                            TextInput::make('description')
                                                ->label('Descrizione')
                                                ->placeholder('Es. Pagamento quota')
                                                ->maxLength(255),

                                            TextInput::make('receipt_number')
                                                ->label('Numero ricevuta')
                                                ->placeholder('Es. RCP-001')
                                                ->maxLength(255),

                                            Textarea::make('notes')
                                                ->label('Note')
                                                ->placeholder('Note sul pagamento...')
                                                ->rows(3)
                                                ->columnSpanFull(),
                                        ]),
                                ]),

                            /*
                            |--------------------------------------------------------------------------
                            | NOTE
                            |--------------------------------------------------------------------------
                            */

                            Section::make('Note')
                                ->description(
                                    'Annotazioni e informazioni aggiuntive sullo studente.'
                                )
                                ->extraAttributes([
                                    'class' => 'border-0 shadow-none rounded-none border-b border-gray-700',
                                ])
                                ->schema([

                                    Textarea::make('notes')
                                        ->label('Note')
                                        ->placeholder(
                                            'Inserisci eventuali annotazioni...'
                                        )
                                        ->rows(7)
                                        ->autosize(),
                                ]),

                            /*
                            |--------------------------------------------------------------------------
                            | DOCUMENTI
                            |--------------------------------------------------------------------------
                            */

                            Section::make('Documenti')
                                ->description(
                                    'Documenti associati allo studente archiviati su storage privato.'
                                )
                                ->extraAttributes([
                                    'class' => 'border-0 shadow-none rounded-none border-t border-gray-700',
                                ])
                                ->columnSpanFull()
                                ->schema([

                                    Repeater::make('attachments')
                                        ->relationship()
                                        ->hiddenLabel()
                                        ->addActionLabel('Aggiungi documento')

                                        /*
                                        |--------------------------------------------------------------------------
                                        | DUE DOCUMENTI PER RIGA
                                        |--------------------------------------------------------------------------
                                        */

                                        ->grid(3)

                                        ->itemLabel(
                                            fn (array $state): string => $state['label']
                                                    ?? $state['file_name']
                                                    ?? 'Nuovo documento'
                                        )

                                        ->collapsible()

                                        ->mutateRelationshipDataBeforeCreateUsing(
                                            function (array $data): array {
                                                $filePath = $data['file_path'] ?? null;

                                                if (! $filePath) {
                                                    return $data;
                                                }

                                                $disk = Storage::disk('s3');

                                                return [
                                                    ...$data,
                                                    'uploaded_by' => auth()->id(),
                                                    'mime_type' => $disk->mimeType($filePath),
                                                    'file_size' => $disk->size($filePath),
                                                ];
                                            }
                                        )

                                        ->schema([

                                            /*
                                            |--------------------------------------------------------------------------
                                            | DESCRIZIONE DOCUMENTO
                                            |--------------------------------------------------------------------------
                                            */

                                            TextInput::make('label')
                                                ->label('Descrizione')
                                                ->placeholder(
                                                    'Es. Documento identità'
                                                )
                                                ->maxLength(255),

                                            /*
                                            |--------------------------------------------------------------------------
                                            | FILE
                                            |--------------------------------------------------------------------------
                                            */

                                            FileUpload::make('file_path')
                                                ->label('File')
                                                ->disk('s3')
                                                ->directory('documents')
                                                ->visibility('private')
                                                ->downloadable()
                                                ->openable()
                                                ->previewable()
                                                ->imagePreviewHeight('180')
                                                ->maxSize(10240)
                                                ->acceptedFileTypes([
                                                    'application/pdf',
                                                    'image/jpeg',
                                                    'image/png',
                                                    'image/webp',
                                                ])
                                                ->storeFileNamesIn('file_name')
                                                ->required(),
                                        ]),
                                ]),
                        ])
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }
}
