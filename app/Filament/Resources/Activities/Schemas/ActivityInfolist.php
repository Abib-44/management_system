<?php

namespace App\Filament\Resources\Activities\Schemas;

use App\Models\Activity;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dettagli attività')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('title')
                            ->label('Titolo')
                            ->columnSpanFull(),
                        TextEntry::make('activity_date')
                            ->label('Data')
                            ->date('d/m/Y')
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('location')
                            ->label('Luogo')
                            ->icon('heroicon-o-map-pin'),
                        TextEntry::make('status')
                            ->label('Stato')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => Activity::STATUS_LABELS[$state] ?? $state)
                            ->color(fn (string $state): string => Activity::STATUS_COLORS[$state] ?? 'gray')
                            ->icon(fn (string $state): string => Activity::STATUS_ICONS[$state] ?? 'heroicon-o-minus'),
                        TextEntry::make('responsible_name')
                            ->label('Responsabile')
                            ->icon('heroicon-o-user')
                            ->placeholder('-'),
                        TextEntry::make('notes')
                            ->label('Note')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
                Section::make('Informazioni di sistema')
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Creata il')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Modificata il')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
