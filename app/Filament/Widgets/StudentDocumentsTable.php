<?php

namespace App\Filament\Widgets;

use App\Models\DocumentArchiveLink;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class StudentDocumentsTable extends BaseWidget
{
    public int|string|null $studentId = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DocumentArchiveLink::query()
                    ->where('student_id', $this->studentId)
                    ->with([
                        'documentArchive.category',
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('documentArchive.title')
                    ->label('Documento')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('documentArchive.category.name')
                    ->label('Categoria')
                    ->badge(),

                Tables\Columns\TextColumn::make('documentArchive.document_date')
                    ->label('Data documento')
                    ->date('d/m/Y'),

                Tables\Columns\TextColumn::make('documentArchive.expires_at')
                    ->label('Scadenza')
                    ->date('d/m/Y'),

                Tables\Columns\TextColumn::make('documentArchive.status')
                    ->label('Stato')
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
