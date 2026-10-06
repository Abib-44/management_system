<?php

namespace App\Filament\Resources\ClassRooms\Tables;

use App\Filament\Resources\ClassRooms\ClassRoomResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ClassRoomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->withCount([
                        'attendances as students_count' => function (Builder $query) {
                            $query->select(DB::raw('count(distinct student_id)'));
                        },
                    ])
                    ->withAvg('grades', 'grade');
            })

            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('schoolYear.name')
                    ->label('Anno scolastico')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('responsible')
                    ->label('Responsabile')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('students_count')
                    ->label('Alunni')
                    ->sortable()
                    ->badge(),

                TextColumn::make('grades_avg_grade')
                    ->label('Media voti')
                    ->sortable()
                    ->numeric(decimalPlaces: 1)
                    ->badge()
                    ->color(fn (?float $state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 6 => 'success',
                        $state >= 5 => 'warning',
                        default => 'danger',
                    }),
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
                fn ($record): string => ClassRoomResource::getUrl(
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
