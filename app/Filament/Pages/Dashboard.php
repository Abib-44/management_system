<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BackupStatus;
use App\Filament\Widgets\DiskUsageWidget;
use App\Filament\Widgets\HardwareTemperature;
use App\Filament\Widgets\ServiceStatus;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use HasPageShield;

    protected static string $routePath = '/system';

    protected static ?string $title = 'Monitoraggio del sistema';

    public function getWidgets(): array
    {
        return [
            HardwareTemperature::class,
            DiskUsageWidget::class,
            BackupStatus::class,
            ServiceStatus::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 4,
            'lg' => 4,
        ];
    }
}
