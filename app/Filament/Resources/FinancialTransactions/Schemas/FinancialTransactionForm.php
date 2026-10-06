<?php

namespace App\Filament\Resources\FinancialTransactions\Schemas;

use App\Models\Activity;
use App\Models\DocumentArchive;
use App\Models\DocumentCategory;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Models\Member;
use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FinancialTransactionForm
{
    private const PAYMENT_METHODS = [
        'cash' => 'Contanti',
        'bank_transfer' => 'Bonifico bancario',
        'card' => 'Carta',
        'check' => 'Assegno',
        'paypal' => 'PayPal',
        'pos' => 'POS',
    ];

    private const SCOPES = [
        'general' => 'Generale',
        'treasury' => 'Tesoreria',
        'school' => 'Scuola',
        'association' => 'Associazione',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Movimento finanziario')
                    ->description(
                        'Inserisci le informazioni relative al movimento finanziario.'
                    )
                    ->icon('heroicon-o-banknotes')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'lg' => 2,
                        ])
                            ->schema([

                                Section::make('Dati del movimento')
                                    ->description(
                                        'Informazioni principali relative all’entrata o all’uscita.'
                                    )
                                    ->columns(2)
                                    ->schema([

                                        Select::make('type')
                                            ->label('Tipo movimento')
                                            ->searchable()
                                            ->getSearchResultsUsing(function (string $search): array {
                                                return FinancialTransaction::query()
                                                    ->select('type')
                                                    ->whereNotNull('type')
                                                    ->where('type', 'like', "%{$search}%")
                                                    ->distinct()
                                                    ->orderBy('type')
                                                    ->pluck('type', 'type')
                                                    ->toArray();
                                            })
                                            ->getOptionLabelUsing(fn ($value): ?string => $value
                                                ? match ($value) {
                                                    'income' => 'Entrata',
                                                    'expense' => 'Uscita',
                                                    default => $value,
                                                }
                                                : null)
                                            ->required()
                                            ->native(false)
                                            ->live()
                                            ->placeholder('Cerca il tipo'),

                                        TextInput::make('amount')
                                            ->label('Importo')
                                            ->numeric()
                                            ->prefix('€')
                                            ->required()
                                            ->minValue(0.01)
                                            ->step(0.01)
                                            ->inputMode('decimal')
                                            ->placeholder('0,00'),

                                        DatePicker::make('transaction_date')
                                            ->label('Data movimento')
                                            ->required()
                                            ->native(false)
                                            ->default(now())
                                            ->displayFormat('d/m/Y'),

                                        Select::make('category_id')
                                            ->label('Categoria')
                                            ->relationship('category', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->native(false)
                                            ->placeholder('Seleziona una categoria')
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nome categoria')
                                                    ->placeholder('Es. Quote associative')
                                                    ->required()
                                                    ->maxLength(255),

                                                Select::make('type')
                                                    ->label('Tipo categoria')
                                                    ->searchable()
                                                    ->getSearchResultsUsing(function (string $search): array {
                                                        return FinancialTransaction::query()
                                                            ->select('type')
                                                            ->whereNotNull('type')
                                                            ->where('type', 'like', "%{$search}%")
                                                            ->distinct()
                                                            ->orderBy('type')
                                                            ->pluck('type', 'type')
                                                            ->toArray();
                                                    })
                                                    ->getOptionLabelUsing(fn ($value): ?string => $value
                                                        ? match ($value) {
                                                            'income' => 'Entrata',
                                                            'expense' => 'Uscita',
                                                            default => $value,
                                                        }
                                                        : null)
                                                    ->native(false)
                                                    ->required(),

                                                TextInput::make('area')
                                                    ->label('Area')
                                                    ->placeholder('Es. Associazione, attività...')
                                                    ->maxLength(255),
                                            ])
                                            ->createOptionUsing(function (array $data) {
                                                return FinancialCategory::create($data)->getKey();
                                            }),

                                        Textarea::make('description')
                                            ->label('Descrizione')
                                            ->placeholder(
                                                'Descrivi brevemente il motivo del movimento...'
                                            )
                                            ->rows(3)
                                            ->autosize()
                                            ->columnSpanFull(),

                                    ])
                                    ->columnSpanFull(),

                                Section::make('Dettagli del pagamento')
                                    ->description(
                                        'Indica come è avvenuto il pagamento e a quale ambito si riferisce.'
                                    )
                                    ->columns(2)
                                    ->schema([

                                        Select::make('payment_method')
                                            ->label('Metodo di pagamento')
                                            ->options(self::PAYMENT_METHODS)
                                            ->default('cash')
                                            ->getOptionLabelUsing(fn (?string $value): ?string => self::PAYMENT_METHODS[$value] ?? $value)
                                            ->searchable()
                                            ->native(false)
                                            ->placeholder('Cerca o aggiungi un metodo')
                                            ->createOptionForm([
                                                TextInput::make('value')
                                                    ->label('Nuovo metodo di pagamento')
                                                    ->placeholder('Es. PayPal, Satispay, Stripe...')
                                                    ->required()
                                                    ->maxLength(255),
                                            ])
                                            ->createOptionUsing(fn (array $data): string => $data['value'])
                                            ->helperText('Puoi scegliere un metodo predefinito oppure aggiungere un nuovo metodo.'),

                                        Select::make('scope')
                                            ->label('Ambito')
                                            ->options(self::SCOPES)
                                            ->getOptionLabelUsing(fn (?string $value): ?string => self::SCOPES[$value] ?? $value)
                                            ->searchable()
                                            ->native(false)
                                            ->placeholder('Cerca o aggiungi un ambito')
                                            ->createOptionForm([
                                                TextInput::make('value')
                                                    ->label('Nuovo ambito')
                                                    ->placeholder('Es. Corso, Evento, Progetto...')
                                                    ->required()
                                                    ->maxLength(255),
                                            ])
                                            ->createOptionUsing(fn (array $data): string => $data['value'])
                                            ->helperText('Puoi scegliere un ambito predefinito oppure aggiungere un nuovo ambito.'),

                                        TextInput::make('receipt_number')
                                            ->label('Numero ricevuta')
                                            ->placeholder('Es. RIC-2026-00125')
                                            ->maxLength(255)
                                            ->helperText(
                                                'Compila se al movimento corrisponde una ricevuta.'
                                            ),

                                    ])
                                    ->columnSpanFull(),

                                Section::make('Documenti')
                                    ->description(
                                        'Documenti associati al movimento archiviati su storage privato.'
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

                                                TextInput::make('label')
                                                    ->label('Descrizione')
                                                    ->placeholder(
                                                        'Es. Ricevuta pagamento'
                                                    )
                                                    ->maxLength(255),

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
                                    ])
                                    ->columnSpanFull(),

                                Section::make('Collegamenti')
                                    ->description(
                                        'Collega il movimento a un socio, studente o attività.'
                                    )
                                    ->schema([

                                        Repeater::make('documentLinks')
                                            ->label('Elementi collegati')
                                            ->relationship()
                                            ->defaultItems(0)
                                            ->reorderable(false)
                                            ->collapsible()
                                            ->addActionLabel('Aggiungi collegamento')
                                            ->itemLabel(function (array $state) {
                                                return match ($state['entity_type'] ?? null) {
                                                    'member' => 'Socio collegato',
                                                    'student' => 'Studente collegato',
                                                    'activity' => 'Attività collegata',
                                                    default => 'Nuovo collegamento',
                                                };
                                            })
                                            ->schema([

                                                Select::make('entity_type')
                                                    ->label('Collega a')
                                                    ->searchable()
                                                    ->getSearchResultsUsing(function (string $search): array {
                                                        return DB::table('document_archive_links')
                                                            ->select('entity_type')
                                                            ->whereNotNull('entity_type')
                                                            ->whereIn('entity_type', ['member', 'student', 'activity'])
                                                            ->where('entity_type', 'like', "%{$search}%")
                                                            ->distinct()
                                                            ->orderBy('entity_type')
                                                            ->pluck('entity_type', 'entity_type')
                                                            ->toArray();
                                                    })
                                                    ->getOptionLabelUsing(fn ($value): ?string => $value
                                                        ? match ($value) {
                                                            'member' => 'Socio',
                                                            'student' => 'Studente',
                                                            'activity' => 'Attività',
                                                            default => $value,
                                                        }
                                                        : null)
                                                    ->native(false)
                                                    ->live()
                                                    ->required()
                                                    ->placeholder('Cerca cosa collegare')
                                                    ->afterStateUpdated(function ($set) {
                                                        $set('member_id', null);
                                                        $set('student_id', null);
                                                        $set('activity_id', null);
                                                    }),

                                                Select::make('member_id')
                                                    ->label('Socio')
                                                    ->searchable()
                                                    ->native(false)
                                                    ->placeholder('Cerca e seleziona un socio')
                                                    ->getSearchResultsUsing(function (string $search): array {
                                                        return Member::query()
                                                            ->where(function ($query) use ($search) {
                                                                $query
                                                                    ->where('first_name', 'like', "%{$search}%")
                                                                    ->orWhere('last_name', 'like', "%{$search}%");
                                                            })
                                                            ->orderBy('last_name')
                                                            ->orderBy('first_name')
                                                            ->limit(50)
                                                            ->get()
                                                            ->mapWithKeys(function (Member $member) {
                                                                return [
                                                                    $member->id => trim(
                                                                        $member->last_name.' '.$member->first_name
                                                                    ),
                                                                ];
                                                            })
                                                            ->toArray();
                                                    })
                                                    ->getOptionLabelUsing(function ($value): ?string {
                                                        $member = Member::find($value);

                                                        return $member
                                                            ? trim($member->last_name.' '.$member->first_name)
                                                            : null;
                                                    })
                                                    ->visible(fn ($get) => $get('entity_type') === 'member')
                                                    ->required(fn ($get) => $get('entity_type') === 'member'),

                                                Select::make('student_id')
                                                    ->label('Studente')
                                                    ->searchable()
                                                    ->native(false)
                                                    ->placeholder('Cerca e seleziona uno studente')
                                                    ->getSearchResultsUsing(function (string $search): array {
                                                        return Student::query()
                                                            ->where(function ($query) use ($search) {
                                                                $query
                                                                    ->where('first_name', 'like', "%{$search}%")
                                                                    ->orWhere('last_name', 'like', "%{$search}%");
                                                            })
                                                            ->orderBy('last_name')
                                                            ->orderBy('first_name')
                                                            ->limit(50)
                                                            ->get()
                                                            ->mapWithKeys(function (Student $student) {
                                                                return [
                                                                    $student->id => trim(
                                                                        $student->last_name.' '.$student->first_name
                                                                    ),
                                                                ];
                                                            })
                                                            ->toArray();
                                                    })
                                                    ->getOptionLabelUsing(function ($value): ?string {
                                                        $student = Student::find($value);

                                                        return $student
                                                            ? trim($student->last_name.' '.$student->first_name)
                                                            : null;
                                                    })
                                                    ->visible(fn ($get) => $get('entity_type') === 'student')
                                                    ->required(fn ($get) => $get('entity_type') === 'student'),

                                                Select::make('activity_id')
                                                    ->label('Attività')
                                                    ->searchable()
                                                    ->native(false)
                                                    ->placeholder('Cerca e seleziona un’attività')
                                                    ->getSearchResultsUsing(function (string $search): array {
                                                        return Activity::query()
                                                            ->where('name', 'like', "%{$search}%")
                                                            ->orderBy('name')
                                                            ->limit(50)
                                                            ->pluck('name', 'id')
                                                            ->toArray();
                                                    })
                                                    ->getOptionLabelUsing(function ($value): ?string {
                                                        return Activity::whereKey($value)->value('name');
                                                    })
                                                    ->visible(fn ($get) => $get('entity_type') === 'activity')
                                                    ->required(fn ($get) => $get('entity_type') === 'activity'),

                                                Select::make('document_archive_id')
                                                    ->label('Documento archiviato')
                                                    ->relationship('documentArchive', 'title')
                                                    ->searchable()
                                                    ->preload()
                                                    ->native(false)
                                                    ->placeholder('Collega eventualmente un documento')
                                                    ->nullable()
                                                    ->createOptionForm([

                                                        TextInput::make('title')
                                                            ->label('Titolo')
                                                            ->placeholder('Es. Ricevuta quota associativa')
                                                            ->required()
                                                            ->maxLength(255),

                                                        Select::make('document_category_id')
                                                            ->label('Categoria documento')
                                                            ->options(function () {
                                                                return DocumentCategory::query()
                                                                    ->orderBy('name')
                                                                    ->pluck('name', 'id');
                                                            })
                                                            ->searchable()
                                                            ->preload()
                                                            ->native(false)
                                                            ->placeholder('Seleziona una categoria')
                                                            ->required()
                                                            ->createOptionForm([
                                                                TextInput::make('name')
                                                                    ->label('Nome categoria')
                                                                    ->placeholder('Es. Ricevute')
                                                                    ->required()
                                                                    ->maxLength(255),
                                                            ]),

                                                        DatePicker::make('date')
                                                            ->label('Data documento')
                                                            ->native(false)
                                                            ->default(now())
                                                            ->required()
                                                            ->displayFormat('d/m/Y'),

                                                        FileUpload::make('file')
                                                            ->label('File')
                                                            ->disk('s3')
                                                            ->directory('documents')
                                                            ->visibility('private')
                                                            ->required()
                                                            ->maxSize(51200)
                                                            ->acceptedFileTypes([
                                                                'application/pdf',
                                                                'application/msword',
                                                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                                                'application/vnd.ms-excel',
                                                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                                                'image/jpeg',
                                                                'image/png',
                                                                'image/webp',
                                                            ])
                                                            ->openable()
                                                            ->downloadable()
                                                            ->previewable()
                                                            ->imagePreviewHeight('180')
                                                            ->panelLayout('integrated'),

                                                    ])
                                                    ->createOptionUsing(function (array $data) {
                                                        return DocumentArchive::create($data)->getKey();
                                                    })
                                                    ->columnSpanFull(),

                                            ])
                                            ->columns(2)
                                            ->columnSpanFull(),

                                    ])
                                    ->columnSpanFull(),

                            ])
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull(),
            ]);
    }
}
