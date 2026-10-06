<?php

namespace App\Filament\Resources\ServiceAssignments\Schemas;

use App\Models\ServiceAssignment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ServiceAssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dettagli assegnazione')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('type')
                            ->label('Tipo')
                            ->options(ServiceAssignment::TYPE_LABELS)
                            ->required()
                            ->live()
                            ->native(false)
                            ->afterStateUpdated(fn (Set $set) => $set('status', null)),
                        Select::make('status')
                            ->label('Stato')
                            ->options(fn (Get $get): array => ServiceAssignment::statusLabelsFor($get('type')))
                            ->required()
                            ->native(false)
                            ->disabled(fn (Get $get): bool => blank($get('type'))),
                        TextInput::make('name')
                            ->label('Servizio / Chiave')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('assignee_name')
                            ->label('Persona')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('document_number')
                            ->label('Documento ID')
                            ->maxLength(255),
                        DatePicker::make('delivered_at')
                            ->label('Consegna')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('returned_at')
                            ->label('Restituzione')
                            ->afterOrEqual('delivered_at')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Textarea::make('notes')
                            ->label('Note')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
