<?php

namespace App\Filament\Resources\Grades\Tables;

use App\Filament\Resources\Grades\GradeResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GradesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.first_name')
                    ->label('Alunno')
                    ->formatStateUsing(
                        fn ($record): string => trim(
                            "{$record->student?->first_name} {$record->student?->last_name}"
                        )
                    )
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                TextColumn::make('classRoom.name')
                    ->label('Classe')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject.name')
                    ->label('Materia')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('grade_date')
                    ->label('Data')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('grade')
                    ->label('Voto')
                    ->numeric(decimalPlaces: 1)
                    ->badge()
                    ->color(
                        fn (float $state): string => match (true) {
                            $state >= 6 => 'success',
                            $state >= 5 => 'warning',
                            default => 'danger',
                        }
                    )
                    ->sortable(),
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
                fn ($record): string => GradeResource::getUrl(
                    'view',
                    ['record' => $record]
                )
            )

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Elimina selezionati'),
                ]),
            ])

            ->defaultSort('grade_date', 'desc');
    }
}
