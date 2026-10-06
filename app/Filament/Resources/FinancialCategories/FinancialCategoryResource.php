<?php

namespace App\Filament\Resources\FinancialCategories;

use App\Filament\Resources\FinancialCategories\Pages\CreateFinancialCategory;
use App\Filament\Resources\FinancialCategories\Pages\EditFinancialCategory;
use App\Filament\Resources\FinancialCategories\Pages\ListFinancialCategories;
use App\Filament\Resources\FinancialCategories\Pages\ViewFinancialCategory;
use App\Filament\Resources\FinancialCategories\Schemas\FinancialCategoryForm;
use App\Filament\Resources\FinancialCategories\Schemas\FinancialCategoryInfolist;
use App\Filament\Resources\FinancialCategories\Tables\FinancialCategoriesTable;
use App\Models\FinancialCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinancialCategoryResource extends Resource
{
    protected static ?string $model = FinancialCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Categorie finanziarie';

    protected static ?string $modelLabel = 'categoria finanziaria';

    protected static ?string $pluralModelLabel = 'categorie finanziarie';

    public static function form(Schema $schema): Schema
    {
        return FinancialCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FinancialCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinancialCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinancialCategories::route('/'),
            'create' => CreateFinancialCategory::route('/create'),
            'view' => ViewFinancialCategory::route('/{record}'),
            'edit' => EditFinancialCategory::route('/{record}/edit'),
        ];
    }
}
