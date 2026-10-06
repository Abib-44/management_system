<?php

namespace App\Filament\Resources\Activities\Tables;

use App\Filament\Resources\Activities\ActivityResource;
use App\Models\Activity;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titolo')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->limit(50),

                TextColumn::make('activity_date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('location')
                    ->label('Luogo')
                    ->searchable()
                    ->icon('heroicon-o-map-pin'),

                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => Activity::STATUS_LABELS[$state] ?? $state
                    )
                    ->color(
                        fn (string $state): string => Activity::STATUS_COLORS[$state] ?? 'gray'
                    )
                    ->icon(
                        fn (string $state): string => Activity::STATUS_ICONS[$state] ?? 'heroicon-o-minus'
                    ),

                TextColumn::make('responsible_name')
                    ->label('Responsabile')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Creata il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Modificata il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Stato')
                    ->options(Activity::STATUS_LABELS),

                SelectFilter::make('responsible_name')
                    ->label('Responsabile')
                    ->options(
                        fn (): array => Activity::responsibleOptions()
                    ),

                Filter::make('activity_period')
                    ->label('Periodo')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dal'),

                        DatePicker::make('until')
                            ->label('Al'),
                    ])
                    ->query(
                        fn (
                            Builder $query,
                            array $data
                        ): Builder => $query
                            ->when(
                                $data['from'] ?? null,
                                fn (
                                    Builder $query,
                                    string $date
                                ): Builder => $query->whereDate(
                                    'activity_date',
                                    '>=',
                                    $date
                                )
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (
                                    Builder $query,
                                    string $date
                                ): Builder => $query->whereDate(
                                    'activity_date',
                                    '<=',
                                    $date
                                )
                            )
                    )
                    ->indicateUsing(
                        fn (array $data): array => array_filter([
                            filled($data['from'] ?? null)
                                ? 'Dal '.Carbon::parse($data['from'])->format('d/m/Y')
                                : null,

                            filled($data['until'] ?? null)
                                ? 'Al '.Carbon::parse($data['until'])->format('d/m/Y')
                                : null,
                        ])
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
                fn (Activity $record): string => ActivityResource::getUrl(
                    'view',
                    ['record' => $record]
                )
            )

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Elimina selezionate'),
                ]),
            ])

            ->defaultSort('activity_date', 'desc')
            ->striped()
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->emptyStateHeading('Nessuna attività')
            ->emptyStateDescription('Non ci sono attività da mostrare.');
    }
}
