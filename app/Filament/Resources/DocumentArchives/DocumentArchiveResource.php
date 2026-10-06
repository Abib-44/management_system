<?php

namespace App\Filament\Resources\DocumentArchives;

use App\Filament\Resources\DocumentArchives\Pages\CreateDocumentArchive;
use App\Filament\Resources\DocumentArchives\Pages\EditDocumentArchive;
use App\Filament\Resources\DocumentArchives\Pages\ListDocumentArchives;
use App\Filament\Resources\DocumentArchives\Pages\ViewDocumentArchive;
use App\Filament\Resources\DocumentArchives\Schemas\DocumentArchiveForm;
use App\Filament\Resources\DocumentArchives\Schemas\DocumentArchiveInfolist;
use App\Filament\Resources\DocumentArchives\Tables\DocumentArchivesTable;
use App\Models\DocumentArchive;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Table;

class DocumentArchiveResource extends Resource
{
    protected static ?string $model = DocumentArchive::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'Archivi documentali';

    protected static ?string $modelLabel = 'archivio documentale';

    protected static ?string $pluralModelLabel = 'archivi documentali';

    public static function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public static function form(Schema $schema): Schema
    {
        return DocumentArchiveForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DocumentArchiveInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentArchivesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocumentArchives::route('/'),
            'create' => CreateDocumentArchive::route('/create'),
            'view' => ViewDocumentArchive::route('/{record}'),
            'edit' => EditDocumentArchive::route('/{record}/edit'),
        ];
    }
}
