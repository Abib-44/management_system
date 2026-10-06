<?php

namespace App\Filament\Resources\DocumentArchives\Schemas;

use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DocumentArchiveInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        ViewEntry::make('document_preview')
                            ->view('filament.Resources.DocumentArchives.document-preview')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
