<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DocumentArchiveStats;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class DocumentArchiveStatistics extends Page
{
    use HasPageShield;

    protected static bool $shouldRegisterNavigation = false;

    protected function getHeaderWidgets(): array
    {
        return [
            DocumentArchiveStats::class,
        ];
    }
}
