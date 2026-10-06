<?php

namespace App\Filament\Resources\MembershipFees;

use App\Filament\Resources\MembershipFees\Pages\CreateMembershipFee;
use App\Filament\Resources\MembershipFees\Pages\EditMembershipFee;
use App\Filament\Resources\MembershipFees\Pages\ListMembershipFees;
use App\Filament\Resources\MembershipFees\Pages\ViewMembershipFee;
use App\Filament\Resources\MembershipFees\Schemas\MembershipFeeForm;
use App\Filament\Resources\MembershipFees\Schemas\MembershipFeeInfolist;
use App\Filament\Resources\MembershipFees\Tables\MembershipFeesTable;
use App\Models\MembershipFee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MembershipFeeResource extends Resource
{
    protected static ?string $model = MembershipFee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'MembershipFee';

    protected static ?string $navigationLabel = 'Quote associative';

    protected static ?string $modelLabel = 'quota associativa';

    protected static ?string $pluralModelLabel = 'quote associative';

    public static function form(Schema $schema): Schema
    {
        return MembershipFeeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MembershipFeeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MembershipFeesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMembershipFees::route('/'),
            'create' => CreateMembershipFee::route('/create'),
            'view' => ViewMembershipFee::route('/{record}'),
            'edit' => EditMembershipFee::route('/{record}/edit'),
        ];
    }
}
