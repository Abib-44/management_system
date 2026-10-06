<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([Section::make('Utente')->description('Gestisci informazioni personali, accesso e autorizzazioni.')->icon('heroicon-o-user')->schema([Fieldset::make('Informazioni personali')->columns(2)->schema([TextInput::make('name')->label('Nome')->placeholder('Nome e cognome')->required()->maxLength(255), TextInput::make('phone')->label('Telefono')->placeholder('+39 333 1234567')->tel()->maxLength(30)]), Fieldset::make('Accesso')->columns(2)->schema([TextInput::make('email')->label('E-mail')->placeholder('nome@esempio.it')->email()->required()->unique(ignoreRecord: true)->maxLength(255), TextInput::make('password')->label('Password')->password()->revealable()->placeholder('Inserisci una password')->minLength(8)->required(fn (string $operation): bool => $operation === 'create')->dehydrated(fn (?string $state): bool => filled($state))->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Lascia vuoto per mantenere la password attuale.' : 'Minimo 8 caratteri.'), TextInput::make('password_confirmation')->label('Conferma password')->password()->revealable()->placeholder('Ripeti la password')->required(fn (string $operation, callable $get): bool => $operation === 'create' || filled($get('password')))->same('password')->dehydrated(false)->helperText('Inserisci nuovamente la password per confermarla.')]), Fieldset::make('Ruolo e autorizzazioni')->schema([Select::make('roles')->label('Ruolo')->relationship('roles', 'name')->multiple()->preload()->searchable()->native(false)->placeholder('Seleziona uno o più ruoli')->columnSpanFull()]), Fieldset::make('Stato dell’account')->columns(2)->schema([Toggle::make('active')->label('Account attivo')->helperText('L’utente può accedere al sistema.')->default(true)->inline(false), DateTimePicker::make('email_verified_at')->label('E-mail verificata il')->placeholder('Non verificata')->native(false)->displayFormat('d/m/Y H:i')])])->columnSpanFull()]);
    }
}
