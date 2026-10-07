<?php

namespace App\Filament\Resources\ServiceAssignments;

use App\Filament\Resources\ServiceAssignments\Pages\CreateServiceAssignment;
use App\Filament\Resources\ServiceAssignments\Pages\EditServiceAssignment;
use App\Filament\Resources\ServiceAssignments\Pages\ListServiceAssignments;
use App\Filament\Resources\ServiceAssignments\Pages\ViewServiceAssignment;
use App\Filament\Resources\ServiceAssignments\Schemas\ServiceAssignmentForm;
use App\Filament\Resources\ServiceAssignments\Tables\ServiceAssignmentsTable;
use App\Models\ServiceAssignment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ServiceAssignmentResource extends Resource
{
    protected static ?string $model = ServiceAssignment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $modelLabel = 'Assegnazione';

    protected static ?string $pluralModelLabel = 'Servizi e chiavi assegnate';

    protected static ?string $navigationLabel = 'Servizi';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ServiceAssignmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceAssignmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceAssignments::route('/'),
            'create' => CreateServiceAssignment::route('/create'),
            'view' => ViewServiceAssignment::route('/{record}'),
            'edit' => EditServiceAssignment::route('/{record}/edit'),
        ];
    }
}
