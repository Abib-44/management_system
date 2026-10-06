<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DiskUsageWidget extends Widget
{
    protected string $view = 'filament.widgets.disk-usage-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 1;

    public function getDiskData(): array
    {
        $total = disk_total_space('/');
        $free = disk_free_space('/');
        $used = $total - $free;

        $percent = $total > 0
            ? round(($used / $total) * 100, 1)
            : 0;

        return [
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'total' => $this->formatBytes($total),
            'percent' => $percent,
        ];
    }

    private function formatBytes(
        int $bytes,
        int $precision = 2
    ): string {
        $units = [
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
        ];

        $bytes = max($bytes, 0);

        $pow = floor(
            ($bytes ? log($bytes) : 0) / log(1024)
        );

        $pow = min(
            $pow,
            count($units) - 1
        );

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }
}
