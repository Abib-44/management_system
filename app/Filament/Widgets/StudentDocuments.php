<?php

namespace App\Filament\Widgets;

use App\Models\Attachment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class StudentDocuments extends BaseWidget
{
    public int $studentId;

    protected static ?string $heading = 'Documenti dello studente';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attachment::query()
                    ->where('attachable_type', 'App\\Models\\DocumentArchive')
                    ->whereIn(
                        'attachable_id',
                        \DB::table('document_archive_links')
                            ->where('student_id', $this->studentId)
                            ->pluck('document_archive_id')
                    )
            )
            ->columns([
                TextColumn::make('file_name')
                    ->label('File')
                    ->searchable(),

                TextColumn::make('label')
                    ->label('Descrizione'),

                TextColumn::make('mime_type')
                    ->label('Tipo'),

                TextColumn::make('file_size')
                    ->label('Dimensione')
                    ->formatStateUsing(
                        fn ($state) => number_format(((int) $state) / 1024 / 1024, 2).' MB'
                    ),

                TextColumn::make('created_at')
                    ->label('Caricato il')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}
