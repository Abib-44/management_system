<?php

namespace App\Filament\Resources\StudentPayments;

use App\Filament\Resources\StudentPayments\Pages\CreateStudentPayment;
use App\Filament\Resources\StudentPayments\Pages\EditStudentPayment;
use App\Filament\Resources\StudentPayments\Pages\ListStudentPayments;
use App\Filament\Resources\StudentPayments\Pages\ViewStudentPayment;
use App\Filament\Resources\StudentPayments\Schemas\StudentPaymentForm;
use App\Filament\Resources\StudentPayments\Schemas\StudentPaymentInfolist;
use App\Filament\Resources\StudentPayments\Tables\StudentPaymentsTable;
use App\Models\StudentPayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StudentPaymentResource extends Resource
{
    protected static ?string $model = StudentPayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'Pagamenti studenti';

    protected static ?string $modelLabel = 'pagamento studente';

    protected static ?string $pluralModelLabel = 'pagamenti studenti';

    public static function form(Schema $schema): Schema
    {
        return StudentPaymentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentPaymentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentPaymentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentPayments::route('/'),
            'create' => CreateStudentPayment::route('/create'),
            'view' => ViewStudentPayment::route('/{record}'),
            'edit' => EditStudentPayment::route('/{record}/edit'),
        ];
    }
}
