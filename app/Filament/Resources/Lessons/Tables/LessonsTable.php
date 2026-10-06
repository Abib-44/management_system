<?php

namespace App\Filament\Resources\Lessons\Tables;

use App\Filament\Resources\Lessons\LessonResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LessonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('classRoom.name')
                    ->label('Classe')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('lesson_date')
                    ->label('Data')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('subject.name')
                    ->label('Materia')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('teacher')
                    ->label('Insegnante')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Non assegnato'),
            ])

            ->filters([
                //
            ])

            ->recordActions([
                EditAction::make()
                    ->label(false)
                    ->icon('heroicon-o-pencil')
                    ->tooltip('Modifica'),

                DeleteAction::make()
                    ->label(false)
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->tooltip('Elimina'),
            ])

            ->recordUrl(
                fn ($record): string => LessonResource::getUrl(
                    'view',
                    ['record' => $record]
                )
            )

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Elimina selezionati'),
                ]),
            ]);
    }
}
