<?php

namespace App\Filament\Resources\Attendances\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AttendanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rilevazione')
                    ->description('Dettagli della rilevazione')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('student.first_name')
                            ->label('Alunno')
                            ->formatStateUsing(
                                fn ($record): string => "{$record->student?->first_name} {$record->student?->last_name}"
                            )
                            ->icon('heroicon-o-user')
                            ->weight('bold')
                            ->url(fn ($record) => $record->student
                                ? route(
                                    'filament.admin.resources.students.view',
                                    $record->student
                                )
                                : null
                            ),

                        TextEntry::make('classRoom.name')
                            ->label('Classe')
                            ->icon('heroicon-o-academic-cap')
                            ->badge()
                            ->color('gray')
                            ->url(fn ($record) => $record->classRoom
                                ? route(
                                    'filament.admin.resources.class-rooms.view',
                                    $record->classRoom
                                )
                                : null
                            ),

                        TextEntry::make('attendance_date')
                            ->label('Data')
                            ->icon('heroicon-o-calendar')
                            ->date('d M Y'),

                        TextEntry::make('status')
                            ->label('Stato')
                            ->badge()
                            ->formatStateUsing(
                                fn (?string $state): string => match ($state) {
                                    'present' => 'Presente',
                                    'absent' => 'Assente',
                                    'late' => 'In ritardo',
                                    default => 'Non specificato',
                                }
                            )
                            ->color(
                                fn (?string $state): string => match ($state) {
                                    'present' => 'success',
                                    'absent' => 'danger',
                                    'late' => 'warning',
                                    default => 'gray',
                                }
                            )
                            ->icon(
                                fn (?string $state): string => match ($state) {
                                    'present' => 'heroicon-o-check-circle',
                                    'absent' => 'heroicon-o-x-circle',
                                    'late' => 'heroicon-o-clock',
                                    default => 'heroicon-o-question-mark-circle',
                                }
                            ),
                    ]),

                Section::make('Note')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('')
                            ->placeholder('Nessuna nota')
                            ->prose()
                            ->columnSpanFull(),
                    ])
                    ->collapsed(fn ($record): bool => empty($record->notes)),

                Section::make('Metadati')
                    ->icon('heroicon-o-clock')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Creato il')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-plus-circle'),

                        TextEntry::make('updated_at')
                            ->label('Ultimo aggiornamento')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-pencil-square'),
                    ]),
            ]);
    }
}
