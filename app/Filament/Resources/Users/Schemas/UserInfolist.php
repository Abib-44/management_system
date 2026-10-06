<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni utente')
                    ->description('Informazioni principali dell’account')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nome')
                            ->icon('heroicon-o-user')
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('email')
                            ->label('E-mail')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->copyMessage('E-mail copiata')
                            ->copyMessageDuration(1500),

                        TextEntry::make('phone')
                            ->label('Telefono')
                            ->icon('heroicon-o-phone')
                            ->placeholder('Non specificato')
                            ->copyable()
                            ->copyMessage('Numero copiato')
                            ->copyMessageDuration(1500),

                        TextEntry::make('active')
                            ->label('Stato account')
                            ->icon('heroicon-o-shield-check')
                            ->badge()
                            ->formatStateUsing(
                                fn (bool $state): string => $state
                                    ? 'Attivo'
                                    : 'Inattivo'
                            )
                            ->color(
                                fn (bool $state): string => $state
                                    ? 'success'
                                    : 'danger'
                            )
                            ->columnSpan(1),

                        TextEntry::make('roles.name')
                            ->label('Ruolo')
                            ->icon('heroicon-o-key')
                            ->badge()
                            ->color('primary')
                            ->placeholder('Nessun ruolo')
                            ->columnSpan(1),
                        TextEntry::make('email_verified_at')
                            ->label('E-mail verificata')
                            ->icon('heroicon-o-check-badge')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Non verificata')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Informazioni del sistema')
                    ->description('Informazioni relative alla gestione dell’account')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Creato il')
                            ->icon('heroicon-o-calendar-days')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Ultima modifica')
                            ->icon('heroicon-o-clock')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
