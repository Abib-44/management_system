<?php

namespace App\Filament\Resources\Lessons\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LessonInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Lezione')
                    ->description('Dettagli generali della lezione')
                    ->icon('heroicon-o-book-open')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('classRoom.name')
                            ->label('Classe')
                            ->icon('heroicon-o-academic-cap')
                            ->badge()
                            ->color('gray')
                            ->url(fn ($record) => $record->classRoom
                                ? route('filament.admin.resources.class-rooms.view', $record->classRoom)
                                : null),

                        TextEntry::make('lesson_date')
                            ->label('Data')
                            ->icon('heroicon-o-calendar')
                            ->date('d M Y'),

                        TextEntry::make('subject.name')
                            ->label('Materia')
                            ->icon('heroicon-o-bookmark')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('teacher')
                            ->label('Insegnante')
                            ->icon('heroicon-o-user')
                            ->placeholder('Non assegnato'),
                    ]),

                Section::make('Argomenti trattati')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('topics')
                            ->label('')
                            ->placeholder('Nessun argomento registrato')
                            ->prose()
                            ->columnSpanFull(),
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
