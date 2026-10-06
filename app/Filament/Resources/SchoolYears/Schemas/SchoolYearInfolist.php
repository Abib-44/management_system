<?php

namespace App\Filament\Resources\SchoolYears\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SchoolYearInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Anno Scolastico')
                    ->description('Dettagli del periodo scolastico')
                    ->icon('heroicon-o-calendar')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nome')
                            ->icon('heroicon-o-identification')
                            ->weight('bold')
                            ->size('lg')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('start_date')
                            ->label('Data inizio')
                            ->icon('heroicon-o-arrow-right-circle')
                            ->date('d M Y'),

                        TextEntry::make('end_date')
                            ->label('Data fine')
                            ->icon('heroicon-o-stop-circle')
                            ->date('d M Y'),

                        TextEntry::make('is_active')
                            ->label('Stato')
                            ->icon('heroicon-o-signal')
                            ->badge()
                            ->formatStateUsing(fn (bool $state) => $state ? 'Attivo' : 'Non attivo')
                            ->color(fn (bool $state) => $state ? 'success' : 'gray'),
                    ]),

                Section::make('Metadati')
                    ->icon('heroicon-o-clock')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Creato il')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-plus-circle'),

                        TextEntry::make('updated_at')
                            ->label('Ultimo aggiornamento')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-pencil-square'),
                    ]),
            ]);
    }
}
