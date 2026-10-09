<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Riepilogo attività')
                ->description('Informazioni principali relative all’evento registrato.')
                ->icon('heroicon-o-clipboard-document-list')
                ->columns(2)
                ->schema([
                    TextEntry::make('action')
                        ->label('Azione eseguita')
                        ->badge()
                        ->color(fn (?string $state): string => match (
                            strtolower($state ?? '')
                        ) {
                            'created', 'create' => 'success',
                            'updated', 'update' => 'warning',
                            'deleted', 'delete' => 'danger',
                            default => 'info',
                        }),

                    TextEntry::make('created_at')
                        ->label('Data e ora')
                        ->dateTime('d/m/Y H:i:s')
                        ->icon('heroicon-o-clock')
                        ->copyable(),

                    TextEntry::make('user.name')
                        ->label('Utente')
                        ->placeholder('Sistema / utente sconosciuto')
                        ->icon('heroicon-o-user'),

                    TextEntry::make('ip_address')
                        ->label('Indirizzo IP')
                        ->placeholder('Non disponibile')
                        ->copyable()
                        ->icon('heroicon-o-globe-alt'),
                ]),

            Section::make('Risorsa coinvolta')
                ->description('Record interessato dall’operazione.')
                ->icon('heroicon-o-circle-stack')
                ->columns(2)
                ->schema([
                    TextEntry::make('subject_type')
                        ->label('Tipo di risorsa')
                        ->formatStateUsing(
                            fn ($state) => $state
                                ? class_basename($state)
                                : 'Non specificato'
                        )
                        ->badge()
                        ->color('gray')
                        ->copyable(),

                    TextEntry::make('subject_id')
                        ->label('ID del record')
                        ->placeholder('Non disponibile')
                        ->copyable(),
                ]),

            Section::make('Dettagli tecnici')
                ->description('Metadati utili per analisi e verifiche.')
                ->icon('heroicon-o-code-bracket')
                ->collapsed()
                ->schema([
                    TextEntry::make('user_agent')
                        ->label('Browser / User Agent')
                        ->placeholder('Non disponibile')
                        ->columnSpanFull()
                        ->copyable(),

                    TextEntry::make('properties')
                        ->label('Proprietà aggiuntive')
                        ->formatStateUsing(
                            fn ($state) => is_array($state)
                                ? json_encode(
                                    $state,
                                    JSON_PRETTY_PRINT
                                    | JSON_UNESCAPED_UNICODE
                                    | JSON_UNESCAPED_SLASHES
                                )
                                : (is_string($state)
                                    ? $state
                                    : json_encode($state, JSON_PRETTY_PRINT))
                        )
                        ->fontFamily('mono')
                        ->columnSpanFull(),
                ])
                ->columns(1),
        ]);
    }
}