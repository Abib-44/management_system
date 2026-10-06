<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Attendance;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Table;

class ViewStudentAttendances extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected static ?string $title = 'Presenze';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->where('student_id', $this->record->id)
            )
            ->columns([
                Tables\Columns\TextColumn::make('attendance_date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('classRoom.name')
                    ->label('Classe')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Note')
                    ->limit(80),
            ])
            ->defaultSort('attendance_date', 'desc');
    }
}
