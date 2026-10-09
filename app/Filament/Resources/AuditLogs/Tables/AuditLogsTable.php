<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Carbon\Carbon;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Utente')
                    ->placeholder('Sistema')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('occurred_at')
                    ->label('Data e ora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('action')
                    ->label('Azione')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'login' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Creato',
                        'updated' => 'Modificato',
                        'deleted' => 'Eliminato',
                        'login' => 'Accesso',
                        'logout' => 'Disconnessione',
                        default => ucfirst($state),
                    })
                    ->searchable(),


                TextColumn::make('ip_address')
                    ->label('Indirizzo IP')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Modificato il')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Azione')
                    ->options([
                        'created' => 'Creato',
                        'updated' => 'Modificato',
                        'deleted' => 'Eliminato',
                        'login' => 'Accesso',
                        'logout' => 'Disconnessione',
                    ])
                    ->multiple(),

                SelectFilter::make('user_id')
                    ->label('Utente')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('occurred_at')
                    ->label('Periodo')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dal')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('until')
                            ->label('Al')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('occurred_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('occurred_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = 'Dal '.Carbon::parse($data['from'])->format('d/m/Y');
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = 'Al '.Carbon::parse($data['until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),

                Filter::make('ip_address')
                    ->label('Indirizzo IP')
                    ->form([
                        TextInput::make('ip_address')
                            ->label('Indirizzo IP'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['ip_address'] ?? null,
                        fn (Builder $query, $ip): Builder => $query->where('ip_address', 'like', "%{$ip}%"),
                    ))
                    ->indicateUsing(fn (array $data): ?string => ($data['ip_address'] ?? null)
                        ? 'IP: '.$data['ip_address']
                        : null),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(false)
                    ->icon('heroicon-o-eye')
                    ->tooltip('Dettaglio'),
            ])
            ->toolbarActions([]);
    }
}