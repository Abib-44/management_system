<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric(),
                DateTimePicker::make('occurred_at')
                    ->required(),
                TextInput::make('action')
                    ->required(),
                Textarea::make('detail')
                    ->columnSpanFull(),
                TextInput::make('ip_address'),
            ]);
    }
}
