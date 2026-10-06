<?php

namespace App\Filament\Resources\Materials\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MaterialInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('subject.name')
                    ->label('Materia')
                    ->icon('heroicon-o-book-open'),

                TextEntry::make('name')
                    ->label('Nome')
                    ->weight('bold')
                    ->icon('heroicon-o-cube'),

                TextEntry::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->placeholder('Non specificato')
                    ->icon('heroicon-o-tag'),

                TextEntry::make('quantity')
                    ->label('Quantità')
                    ->numeric()
                    ->suffix(' unità')
                    ->icon('heroicon-o-archive-box'),

                TextEntry::make('available_quantity')
                    ->label('Quantità disponibile')
                    ->numeric()
                    ->suffix(' unità')
                    ->icon('heroicon-o-check-circle'),

                TextEntry::make('location')
                    ->label('Posizione')
                    ->placeholder('Non specificata')
                    ->icon('heroicon-o-map-pin'),

                IconEntry::make('active')
                    ->label('Materiale attivo')
                    ->boolean(),

                TextEntry::make('description')
                    ->label('Descrizione')
                    ->placeholder('Nessuna descrizione')
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label('Ultima modifica')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),
            ]);
    }
}
