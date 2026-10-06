<?php

namespace App\Filament\Resources\Members\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make()
                ->extraAttributes([
                    'class' => 'member-main-container',
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
                                    'Dati anagrafici e principali informazioni di contatto del socio.'
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

                                    TextInput::make('phone')
                                        ->label('Telefono')
                                        ->placeholder('+39 000 000 0000')
                                        ->tel()
                                        ->maxLength(50),

                                    TextInput::make('email')
                                        ->label('Email')
                                        ->placeholder('nome@esempio.it')
                                        ->email()
                                        ->maxLength(255),
                                ]),

                            /*
                            |--------------------------------------------------------------------------
                            | STATO ASSOCIATIVO
                            |--------------------------------------------------------------------------
                            */

                            Section::make('Stato associativo')
                                ->description(
                                    'Gestisci il periodo di iscrizione e lo stato attuale del socio.'
                                )
                                ->extraAttributes([
                                    'class' => 'border-0 shadow-none rounded-none border-b border-gray-700',
                                ])
                                ->columns(2)
                                ->schema([

                                    DatePicker::make('registration_date')
                                        ->label('Data iscrizione')
                                        ->native(false)
                                        ->displayFormat('d/m/Y'),

                                    DatePicker::make('renewal_date')
                                        ->label('Data rinnovo')
                                        ->native(false)
                                        ->displayFormat('d/m/Y'),

                                    Select::make('status')
                                        ->label('Stato socio')
                                        ->options([
                                            'active' => 'Attivo',
                                            'inactive' => 'Inattivo',
                                        ])
                                        ->native(false)
                                        ->required(),

                                    Select::make('assembly_status')
                                        ->label('Stato assemblea')
                                        ->options([
                                            'active' => 'Attivo',
                                            'inactive' => 'Inattivo',
                                        ])
                                        ->native(false)
                                        ->required(),
                                ]),

                            /*
                            |--------------------------------------------------------------------------
                            | SITUAZIONE FINANZIARIA
                            |--------------------------------------------------------------------------
                            */

                            Section::make('Situazione finanziaria')
                                ->description(
                                    'Riepilogo della quota associativa e della situazione dei pagamenti.'
                                )
                                ->extraAttributes([
                                    'class' => 'border-0 shadow-none rounded-none border-b border-gray-700',
                                ])
                                ->columns(2)
                                ->schema([

                                    TextInput::make('annual_fee')
                                        ->label('Quota annuale')
                                        ->placeholder('0,00')
                                        ->numeric()
                                        ->prefix('€')
                                        ->required()
                                        ->minValue(0)
                                        ->step(0.01),

                                    Placeholder::make('payment_status_display')
                                        ->label('Stato pagamento')
                                        ->content(function ($record): string {
                                            if (! $record) {
                                                return 'Non ancora disponibile';
                                            }

                                            return match ($record->payment_status) {
                                                'paid' => 'Pagato',
                                                'partially_paid' => 'Parzialmente pagato',
                                                'not_paid' => 'Non pagato',
                                                default => 'Non pagato',
                                            };
                                        }),

                                    Placeholder::make('paid_amount')
                                        ->label('Totale pagato')
                                        ->content(function ($record): string {
                                            if (! $record) {
                                                return '€ 0,00';
                                            }

                                            return number_format(
                                                $record->paid_amount,
                                                2,
                                                ',',
                                                '.'
                                            ).' €';
                                        }),

                                    Placeholder::make('remaining_amount')
                                        ->label('Importo residuo')
                                        ->content(function ($record): string {
                                            if (! $record) {
                                                return '€ 0,00';
                                            }

                                            return number_format(
                                                $record->remaining_amount,
                                                2,
                                                ',',
                                                '.'
                                            ).' €';
                                        }),
                                ]),

                            /*
                            |--------------------------------------------------------------------------
                            | NOTE
                            |--------------------------------------------------------------------------
                            */

                            Section::make('Note')
                                ->description(
                                    'Annotazioni interne e informazioni aggiuntive sul socio.'
                                )
                                ->extraAttributes([
                                    'class' => 'border-0 shadow-none rounded-none border-b border-gray-700',
                                ])
                                ->schema([

                                    Textarea::make('activity_notes')
                                        ->label('Note')
                                        ->placeholder(
                                            'Inserisci eventuali annotazioni...'
                                        )
                                        ->rows(7)
                                        ->autosize(),
                                ]),

                            /*
                            |--------------------------------------------------------------------------
                            | DOCUMENTI — FULL WIDTH
                            |--------------------------------------------------------------------------
                            */

                            Section::make('Documenti')
                                ->description(
                                    'Documenti associati al socio archiviati su storage privato.'
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
                                        ->itemLabel(false)

                                        /*
                                        |--------------------------------------------------------------------------
                                        | 2 DOCUMENTI AFFIANCATI
                                        |--------------------------------------------------------------------------
                                        */

                                        ->grid(3)

                                        ->extraAttributes([
                                            'class' => 'documents-repeater',
                                        ])

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
                                                    'version' => $data['version'] ?? 1,
                                                ];
                                            }
                                        )

                                        ->schema([

                                            TextInput::make('label')
                                                ->label('Descrizione')
                                                ->placeholder('Es. Documento identità')
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
                                ]),
                        ])
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }
}
