<?php

namespace App\Filament\Resources\FinancialTransactions;

use App\Filament\Resources\FinancialTransactions\Pages\CreateFinancialTransaction;
use App\Filament\Resources\FinancialTransactions\Pages\EditFinancialTransaction;
use App\Filament\Resources\FinancialTransactions\Pages\ListFinancialTransactions;
use App\Filament\Resources\FinancialTransactions\Pages\ViewFinancialTransaction;
use App\Filament\Resources\FinancialTransactions\Schemas\FinancialTransactionForm;
use App\Filament\Resources\FinancialTransactions\Schemas\FinancialTransactionInfolist;
use App\Filament\Resources\FinancialTransactions\Tables\FinancialTransactionsTable;
use App\Models\FinancialTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FinancialTransactionResource extends Resource
{
    protected static ?string $model = FinancialTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'Movimenti finanziari';

    protected static ?string $modelLabel = 'movimento finanziario';

    protected static ?string $pluralModelLabel = 'movimenti finanziari';

    public static function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    /**
     * Un utente "solo scuola" (teacher senza altri ruoli finanziari)
     * vede, apre e modifica solo i movimenti con scope = school.
     * Vale per tabella, view, edit, azioni e ricerca globale.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->isSchoolOnly()) {
            $query
                ->where('scope', 'school')
                ->whereHas(
                    'category',
                    fn (Builder $q) => $q->visibleTo(auth()->user())
                );
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return FinancialTransactionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FinancialTransactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinancialTransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinancialTransactions::route('/'),
            'create' => CreateFinancialTransaction::route('/create'),
            'view' => ViewFinancialTransaction::route('/{record}'),
            'edit' => EditFinancialTransaction::route('/{record}/edit'),
        ];
    }
}