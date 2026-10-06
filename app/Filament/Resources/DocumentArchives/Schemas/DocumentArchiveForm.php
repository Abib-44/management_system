<?php

namespace App\Filament\Resources\DocumentArchives\Schemas;

use App\Models\Activity;
use App\Models\DocumentCategory;
use App\Models\FinancialTransaction;
use App\Models\Member;
use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DocumentArchiveForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Documento')
                    ->description('Inserisci le informazioni del documento.')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'lg' => 2,
                    ])
                    ->schema([

                        TextInput::make('title')
                            ->label('Titolo documento')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('document_category_id')
                            ->label('Categoria')
                            ->relationship(
                                'category',
                                'name',
                                modifyQueryUsing: fn ($query) => $query->where('active', true)
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nome')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(DocumentCategory::class, 'name'),

                                Toggle::make('active')
                                    ->label('Attiva')
                                    ->default(true),
                            ])
                            ->createOptionModalHeading('Nuova categoria')
                            ->required(),

                        Select::make('status')
                            ->label('Stato')
                            ->options([
                                'active' => 'Attivo',
                                'archived' => 'Archiviato',
                                'expired' => 'Scaduto',
                            ])
                            ->default('active')
                            ->native(false)
                            ->required(),

                        DatePicker::make('document_date')
                            ->label('Data documento')
                            ->displayFormat('d/m/Y')
                            ->native(false),

                        DatePicker::make('expires_at')
                            ->label('Data scadenza')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->afterOrEqual('document_date'),

                        Repeater::make('document_links')
                            ->label('Collegamenti')
                            ->schema([

                                Select::make('entity_type')
                                    ->label('Tipo')
                                    ->options([
                                        'member' => 'Socio',
                                        'student' => 'Studente',
                                        'activity' => 'Attività',
                                        'transaction' => 'Movimento finanziario',
                                    ])
                                    ->default('student')
                                    ->native(false)
                                    ->required()
                                    ->live(),

                                Select::make('member_id')
                                    ->label('Socio')
                                    ->options(
                                        fn () => Member::query()
                                            ->orderBy('last_name')
                                            ->orderBy('first_name')
                                            ->get()
                                            ->mapWithKeys(
                                                fn ($member) => [
                                                    $member->id => "{$member->last_name} {$member->first_name}",
                                                ]
                                            )
                                    )
                                    ->searchable()
                                    ->native(false)
                                    ->visible(
                                        fn ($get) => $get('entity_type') === 'member'
                                    )
                                    ->required(
                                        fn ($get) => $get('entity_type') === 'member'
                                    ),

                                Select::make('student_id')
                                    ->label('Studente')
                                    ->options(
                                        fn () => Student::query()
                                            ->orderBy('last_name')
                                            ->orderBy('first_name')
                                            ->get()
                                            ->mapWithKeys(
                                                fn ($student) => [
                                                    $student->id => "{$student->last_name} {$student->first_name}",
                                                ]
                                            )
                                    )
                                    ->searchable()
                                    ->native(false)
                                    ->visible(
                                        fn ($get) => $get('entity_type') === 'student'
                                    )
                                    ->required(
                                        fn ($get) => $get('entity_type') === 'student'
                                    ),

                                Select::make('activity_id')
                                    ->label('Attività')
                                    ->options(
                                        fn () => Activity::query()
                                            ->orderBy('title')
                                            ->pluck('title', 'id')
                                    )
                                    ->searchable()
                                    ->native(false)
                                    ->visible(
                                        fn ($get) => $get('entity_type') === 'activity'
                                    )
                                    ->required(
                                        fn ($get) => $get('entity_type') === 'activity'
                                    ),

                                Select::make('transaction_id')
                                    ->label('Movimento finanziario')
                                    ->options(
                                        fn () => FinancialTransaction::query()
                                            ->orderByDesc('transaction_date')
                                            ->get()
                                            ->mapWithKeys(
                                                fn ($transaction) => [
                                                    $transaction->id => "{$transaction->transaction_date->format('d/m/Y')} - {$transaction->description}",
                                                ]
                                            )
                                    )
                                    ->searchable()
                                    ->native(false)
                                    ->visible(
                                        fn ($get) => $get('entity_type') === 'transaction'
                                    )
                                    ->required(
                                        fn ($get) => $get('entity_type') === 'transaction'
                                    ),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->addActionLabel('Aggiungi collegamento')
                            ->reorderable(false)
                            ->collapsible(false)
                            ->columnSpanFull(),

                        Repeater::make('uploaded_files')
                            ->label('Ricevute / Allegati')
                            ->addActionLabel('Aggiungi allegato')
                            ->grid(3)
                            ->schema([

                                TextInput::make('description')
                                    ->label('Descrizione')
                                    ->placeholder('Es. Carta identità')
                                    ->maxLength(255),

                                FileUpload::make('file')
                                    ->label('File')
                                    ->disk('s3')
                                    ->directory('documents')
                                    ->visibility('private')
                                    ->downloadable()
                                    ->openable()
                                    ->previewable()
                                    ->imagePreviewHeight('180')
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
                                    ->required(),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
