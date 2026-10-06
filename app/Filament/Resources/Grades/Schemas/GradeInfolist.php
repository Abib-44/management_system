<?php

namespace App\Filament\Resources\Grades\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GradeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Valutazione')
                    ->description('Dettagli del voto assegnato')
                    ->icon('heroicon-o-star')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('student.first_name')
                            ->label('Alunno')
                            ->formatStateUsing(
                                fn ($record) => "{$record->student?->first_name} {$record->student?->last_name}"
                            )
                            ->icon('heroicon-o-user')
                            ->weight('bold')
                            ->url(
                                fn ($record) => $record->student
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
                            ->url(
                                fn ($record) => $record->classRoom
                                    ? route(
                                        'filament.admin.resources.class-rooms.view',
                                        $record->classRoom
                                    )
                                    : null
                            ),

                        TextEntry::make('lesson.subject')
                            ->label('Lezione collegata')
                            ->icon('heroicon-o-book-open')
                            ->placeholder('Nessuna lezione collegata'),

                        TextEntry::make('subject')
                            ->label('Materia')
                            ->icon('heroicon-o-bookmark')
                            ->formatStateUsing(function ($state) {
                                if (is_array($state)) {
                                    return $state['name'] ?? 'Materia non specificata';
                                }

                                if (is_object($state)) {
                                    return $state->name ?? 'Materia non specificata';
                                }

                                return $state ?: 'Materia non specificata';
                            })
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('grade_date')
                            ->label('Data valutazione')
                            ->icon('heroicon-o-calendar')
                            ->date('d M Y'),

                        TextEntry::make('grade')
                            ->label('Voto')
                            ->icon('heroicon-o-star')
                            ->numeric(decimalPlaces: 1)
                            ->badge()
                            ->size('lg')
                            ->color(
                                fn (float $state): string => match (true) {
                                    $state >= 6 => 'success',
                                    $state >= 5 => 'warning',
                                    default => 'danger',
                                }
                            ),
                    ]),

                Section::make('Note')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('')
                            ->placeholder('Nessuna nota')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(fn ($record) => empty($record->notes)),

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