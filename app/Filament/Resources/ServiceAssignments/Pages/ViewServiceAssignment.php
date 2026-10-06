<?php

namespace App\Filament\Resources\ServiceAssignments\Pages;

use App\Filament\Resources\ServiceAssignments\ServiceAssignmentResource;
use App\Models\ServiceAssignment;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewServiceAssignment extends ViewRecord
{
    protected static string $resource = ServiceAssignmentResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dettagli assegnazione')
                ->description('Informazioni relative al servizio o alla chiave assegnata.')
                ->icon('heroicon-o-key')
                ->schema([
                    TextEntry::make('type')
                        ->label('Tipo')
                        ->badge()
                        ->formatStateUsing(
                            fn ($state) => ServiceAssignment::TYPE_LABELS[$state] ?? $state
                        )
                        ->color('primary'),

                    TextEntry::make('name')
                        ->label('Servizio / Chiave')
                        ->weight('bold')
                        ->icon('heroicon-o-tag'),

                    TextEntry::make('assignee_name')
                        ->label('Persona')
                        ->icon('heroicon-o-user'),

                    TextEntry::make('document_number')
                        ->label('Documento ID')
                        ->placeholder('Non specificato')
                        ->icon('heroicon-o-identification'),
                ])
                ->columns(2),

            Section::make('Consegna e restituzione')
                ->description('Stato e date di gestione dell\'assegnazione.')
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    TextEntry::make('delivered_at')
                        ->label('Data consegna')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('Non specificata')
                        ->icon('heroicon-o-arrow-up-right'),

                    TextEntry::make('returned_at')
                        ->label('Data restituzione')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('Non restituito')
                        ->icon('heroicon-o-arrow-down-left'),

                    TextEntry::make('status')
                        ->label('Stato')
                        ->badge()
                        ->formatStateUsing(
                            fn ($state) => ServiceAssignment::allStatusLabels()[$state] ?? $state
                        )
                        ->color(
                            fn ($state) => match ($state) {
                                'active' => 'success',
                                'returned' => 'gray',
                                'overdue' => 'danger',
                                default => 'gray',
                            }
                        ),
                ])
                ->columns(3),

            Section::make('Informazioni sistema')
                ->icon('heroicon-o-information-circle')
                ->collapsed()
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Creato il')
                        ->dateTime('d/m/Y H:i'),

                    TextEntry::make('updated_at')
                        ->label('Ultima modifica')
                        ->dateTime('d/m/Y H:i'),
                ])
                ->columns(2),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Modifica'),

            DeleteAction::make()
                ->label('Elimina'),
        ];
    }
}