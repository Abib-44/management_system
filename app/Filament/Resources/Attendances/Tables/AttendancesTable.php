<?php

namespace App\Filament\Resources\Attendances\Tables;

use App\Filament\Resources\Attendances\AttendanceResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('student.first_name')
                    ->label('Alunno')
                    ->formatStateUsing(
                        fn ($record): string => "{$record->student?->first_name} {$record->student?->last_name}"
                    )
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                TextColumn::make('classRoom.name')
                    ->label('Classe')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('attendance_date')
                    ->label('Data')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'present' => 'Presente',
                        'absent' => 'Assente',
                        'late' => 'In ritardo',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'present' => 'heroicon-o-check-circle',
                        'absent' => 'heroicon-o-x-circle',
                        'late' => 'heroicon-o-clock',
                        default => 'heroicon-o-question-mark-circle',
                    })
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
                fn ($record): string => AttendanceResource::getUrl(
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
