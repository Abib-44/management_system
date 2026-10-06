<?php

namespace App\Filament\Resources\DocumentArchives\Tables;

use App\Filament\Resources\DocumentArchives\DocumentArchiveResource;
use App\Models\DocumentCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentArchivesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titolo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Categoria documento')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('document_date')
                    ->label('Data emissione')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Data scadenza')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Stato')
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'active' => 'Attivo',
                            'archived' => 'Archiviato',
                            default => $state,
                        }
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Creato da')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updater.name')
                    ->label('Modificato da')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Data inserimento')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Ultima modifica')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                SelectFilter::make('document_category_id')
                    ->label('Categoria')
                    ->options(
                        DocumentCategory::query()
                            ->where('active', true)
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('Stato')
                    ->options([
                        'active' => 'Attivo',
                        'archived' => 'Archiviato',
                    ]),

                Filter::make('document_date')
                    ->label('Data emissione')
                    ->form([
                        DatePicker::make('from')
                            ->label('Da'),

                        DatePicker::make('until')
                            ->label('A'),
                    ])
                    ->query(
                        function (Builder $query, array $data): Builder {
                            return $query
                                ->when(
                                    $data['from'] ?? null,
                                    fn (Builder $query, $date): Builder => $query->whereDate(
                                        'document_date',
                                        '>=',
                                        $date
                                    )
                                )
                                ->when(
                                    $data['until'] ?? null,
                                    fn (Builder $query, $date): Builder => $query->whereDate(
                                        'document_date',
                                        '<=',
                                        $date
                                    )
                                );
                        }
                    ),

                Filter::make('expires_at')
                    ->label('Data scadenza')
                    ->form([
                        DatePicker::make('from')
                            ->label('Da'),

                        DatePicker::make('until')
                            ->label('A'),
                    ])
                    ->query(
                        function (Builder $query, array $data): Builder {
                            return $query
                                ->when(
                                    $data['from'] ?? null,
                                    fn (Builder $query, $date): Builder => $query->whereDate(
                                        'expires_at',
                                        '>=',
                                        $date
                                    )
                                )
                                ->when(
                                    $data['until'] ?? null,
                                    fn (Builder $query, $date): Builder => $query->whereDate(
                                        'expires_at',
                                        '<=',
                                        $date
                                    )
                                );
                        }
                    ),
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
                fn ($record): string => DocumentArchiveResource::getUrl(
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

            ->defaultSort('created_at', 'desc');
    }
}
