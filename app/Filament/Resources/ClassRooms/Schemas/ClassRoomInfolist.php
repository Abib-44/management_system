<?php

namespace App\Filament\Resources\ClassRooms\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClassRoomInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni classe')
                    ->description('Dettagli generali della classe')
                    ->icon('heroicon-o-academic-cap')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nome classe')
                            ->icon('heroicon-o-identification')
                            ->weight('bold')
                            ->size('lg')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('schoolYear.name')
                            ->label('Anno scolastico')
                            ->icon('heroicon-o-calendar')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('responsible')
                            ->label('Responsabile')
                            ->icon('heroicon-o-user')
                            ->placeholder('Non assegnato'),
                    ]),

                Section::make('Statistiche')
                    ->icon('heroicon-o-chart-bar')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('students_count')
                            ->label('Numero alunni')
                            ->state(fn ($record) => $record->attendances()->distinct('student_id')->count('student_id'))
                            ->icon('heroicon-o-users')
                            ->badge()
                            ->color('success')
                            ->size('lg'),

                        TextEntry::make('grades_avg')
                            ->label('Media voti')
                            ->state(fn ($record) => $record->grades()->avg('grade'))
                            ->icon('heroicon-o-star')
                            ->numeric(decimalPlaces: 1)
                            ->placeholder('Nessun voto')
                            ->badge()
                            ->color(fn (?float $state): string => match (true) {
                                $state === null => 'gray',
                                $state >= 6 => 'success',
                                $state >= 5 => 'warning',
                                default => 'danger',
                            })
                            ->size('lg'),
                    ]),

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
