<?php

namespace App\Filament\Resources\Students\Tables;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Student;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query): Builder => $query
                    ->with('classRoom')
                    ->withSum('payments', 'amount')
            )

            ->columns([
                TextColumn::make('full_name')
                    ->label('Studente')
                    ->state(
                        fn (Student $record): string => "{$record->first_name} {$record->last_name}"
                    )
                    ->searchable(
                        query: function (
                            Builder $query,
                            string $search
                        ): Builder {
                            return $query->where(
                                fn (Builder $query) => $query
                                    ->where(
                                        'first_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'last_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhereRaw(
                                        "CONCAT(first_name, ' ', last_name) LIKE ?",
                                        ["%{$search}%"]
                                    )
                            );
                        }
                    )
                    ->sortable(['last_name', 'first_name'])
                    ->weight('bold'),

                TextColumn::make('classRoom.name')
                    ->label('Classe')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('academic_record')
                    ->label('Scheda valutazione')
                    ->state('Apri scheda')
                    ->url(
                        fn (Student $record): string => StudentResource::getUrl(
                            'academic-record',
                            ['record' => $record]
                        )
                    )
                    ->color('primary')
                    ->weight('bold'),

                TextColumn::make('birth_date')
                    ->label('Data di nascita')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('father_name')
                    ->label('Nome padre')
                    ->searchable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('father_phone')
                    ->label('Telefono padre')
                    ->searchable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('mother_name')
                    ->label('Nome madre')
                    ->searchable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('mother_phone')
                    ->label('Telefono madre')
                    ->searchable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('address')
                    ->label('Indirizzo')
                    ->searchable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('total_fee')
                    ->label('Quota')
                    ->money('EUR')
                    ->sortable(),

                TextColumn::make('paid_amount')
                    ->label('Pagato')
                    ->state(
                        fn (Student $record): float => $record->paid_amount
                    )
                    ->money('EUR'),

                TextColumn::make('remaining_amount')
                    ->label('Residuo')
                    ->state(
                        fn (Student $record): float => $record->remaining_amount
                    )
                    ->money('EUR')
                    ->color(
                        fn (Student $record): string => match (
                            $record->payment_status
                        ) {
                            'regular',
                            'paid' => 'success',

                            'partially_paid' => 'warning',

                            'not_paid' => 'danger',

                            default => 'gray',
                        }
                    ),

                TextColumn::make('installment_plan')
                    ->label('Piano rate')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'no_interest' => 'Nessuna rata',
                            'installment_1' => '1 rata',
                            'installment_2' => '2 rate',
                            default => 'Non specificato',
                        }
                    )
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('payment_status')
                    ->label('Pagamento')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'regular',
                            'paid' => 'Pagato',

                            'partially_paid' => 'Parzialmente pagato',

                            'not_paid' => 'Non pagato',

                            default => 'Non specificato',
                        }
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'regular',
                            'paid' => 'success',

                            'partially_paid' => 'warning',

                            'not_paid' => 'danger',

                            default => 'gray',
                        }
                    ),

                TextColumn::make('notes')
                    ->label('Note')
                    ->limit(50)
                    ->tooltip(
                        fn (Student $record): ?string => $record->notes
                    )
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('updated_at')
                    ->label('Modificato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])

            ->filters([
                SelectFilter::make('class_room_id')
                    ->label('Classe')
                    ->relationship(
                        'classRoom',
                        'name'
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('payment_status')
                    ->label('Pagamento')
                    ->options([
                        'regular' => 'Pagato',
                        'partially_paid' => 'Parzialmente pagato',
                        'not_paid' => 'Non pagato',
                    ])
                    ->query(
                        function (
                            Builder $query,
                            array $data
                        ): Builder {
                            $status = $data['value'] ?? null;

                            if (! $status) {
                                return $query;
                            }

                            return match ($status) {
                                'regular' => $query->whereRaw(
                                    'COALESCE(payments_sum_amount, 0) >= total_fee'
                                ),

                                'partially_paid' => $query->whereRaw(
                                    'COALESCE(payments_sum_amount, 0) > 0
                                    AND COALESCE(payments_sum_amount, 0) < total_fee'
                                ),

                                'not_paid' => $query->whereRaw(
                                    'COALESCE(payments_sum_amount, 0) <= 0'
                                ),

                                default => $query,
                            };
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
                fn (Student $record): string => StudentResource::getUrl(
                    'view',
                    ['record' => $record]
                )
            )

            ->toolbarActions([])

            ->defaultSort(
                'last_name',
                'asc'
            );
    }
}
