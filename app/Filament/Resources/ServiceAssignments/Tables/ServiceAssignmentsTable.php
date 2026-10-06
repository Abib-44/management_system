<?php

namespace App\Filament\Resources\ServiceAssignments\Tables;

use App\Models\ServiceAssignment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceAssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => ServiceAssignment::TYPE_LABELS[$state] ?? $state
                    )
                    ->color(
                        fn (string $state): string => ServiceAssignment::TYPE_COLORS[$state] ?? 'gray'
                    )
                    ->icon(
                        fn (string $state): string => ServiceAssignment::TYPE_ICONS[$state] ?? 'heroicon-o-minus'
                    ),

                TextColumn::make('name')
                    ->label('Servizio/Chiave')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold),

                TextColumn::make('assignee_name')
                    ->label('Persona')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user'),

                TextColumn::make('document_number')
                    ->label('Documento ID')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('delivered_at')
                    ->label('Consegna')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('returned_at')
                    ->label('Restituzione')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => ServiceAssignment::allStatusLabels()[$state] ?? $state
                    )
                    ->color(
                        fn (string $state): string => ServiceAssignment::STATUS_COLORS[$state] ?? 'gray'
                    ),

                TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Modificato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->recordUrl(
                fn (ServiceAssignment $record): string =>
                    route(
                        'filament.admin.resources.service-assignments.view',
                        ['record' => $record]
                    )
            )

            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(ServiceAssignment::TYPE_LABELS),

                SelectFilter::make('status')
                    ->label('Stato')
                    ->options(ServiceAssignment::allStatusLabels()),

                Filter::make('not_returned')
                    ->label('Non restituiti')
                    ->toggle()
                    ->query(
                        fn (Builder $query): Builder => $query->whereNull('returned_at')
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

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Elimina selezionati'),
                ]),
            ])

            ->searchPlaceholder('Cerca servizi o chiavi...')
            ->defaultSort('delivered_at', 'desc')
            ->striped()
            ->emptyStateIcon('heroicon-o-key')
            ->emptyStateHeading('Nessun servizio o chiave assegnati')
            ->emptyStateDescription(
                'Crea la prima assegnazione con il pulsante Servizio.'
            );
    }
}