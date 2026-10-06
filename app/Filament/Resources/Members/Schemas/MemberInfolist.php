<?php

namespace App\Filament\Resources\Members\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MemberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // =====================================================
                // DATI PERSONALI
                // =====================================================

                Section::make('Dati personali')
                    ->description('Informazioni principali del socio')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextEntry::make('first_name')
                            ->label('Nome')
                            ->weight('bold'),

                        TextEntry::make('last_name')
                            ->label('Cognome')
                            ->weight('bold'),

                        TextEntry::make('phone')
                            ->label('Telefono')
                            ->placeholder('-')
                            ->icon('heroicon-o-phone'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('-')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->copyMessage('Email copiata')
                            ->copyMessageDuration(1500),
                    ])
                    ->columns(2),

                // =====================================================
                // ISCRIZIONE
                // =====================================================

                Section::make('Iscrizione')
                    ->description('Informazioni relative all’iscrizione e al rinnovo')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        TextEntry::make('registration_date')
                            ->label('Data di iscrizione')
                            ->date('d/m/Y')
                            ->placeholder('-'),

                        TextEntry::make('renewal_date')
                            ->label('Data di rinnovo')
                            ->date('d/m/Y')
                            ->placeholder('-'),

                        TextEntry::make('status')
                            ->label('Stato')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'active' => 'Attivo',
                                'inactive' => 'Inattivo',
                                'suspended' => 'Sospeso',
                                'expired' => 'Scaduto',
                                default => $state ?? 'Non specificato',
                            })
                            ->color(fn (?string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'gray',
                                'suspended' => 'warning',
                                'expired' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('assembly_status')
                            ->label('Stato assemblea')
                            ->badge()
                            ->placeholder('-')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'regular' => 'Regolare',
                                'present' => 'Presente',
                                'absent' => 'Assente',
                                default => $state ?? 'Non specificato',
                            })
                            ->color(fn (?string $state): string => match ($state) {
                                'regular' => 'success',
                                'present' => 'success',
                                'absent' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),

                // =====================================================
                // QUOTA
                // =====================================================

                Section::make('Quota')
                    ->description('Informazioni relative alla quota associativa')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        TextEntry::make('annual_fee')
                            ->label('Quota annuale')
                            ->money('EUR', locale: 'it_IT')
                            ->placeholder('-')
                            ->weight('bold'),
                    ])
                    ->columns(2),

                // =====================================================
                // NOTE
                // =====================================================

                Section::make('Note attività')
                    ->description('Annotazioni relative al socio')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('activity_notes')
                            ->label('Note')
                            ->placeholder('Nessuna nota disponibile')
                            ->columnSpanFull(),
                    ]),

                // =====================================================
                // INFORMAZIONI SISTEMA
                // =====================================================

                Section::make('Informazioni di sistema')
                    ->description('Informazioni tecniche del record')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Creato il')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Modificato il')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
