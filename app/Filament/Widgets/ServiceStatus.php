<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ServiceStatus extends Widget
{
    protected string $view = 'filament.widgets.service-status';

    protected int|string|array $columnSpan = 'full';

    private const TIMEOUT_SECONDS = 0.5;

    private const SERVICES = [
        'Database' => [
            'host' => 'mysql',
            'port' => 3306,
            'icon' => 'heroicon-o-circle-stack',
            'description' => 'MySQL database',
        ],
        'MinIO' => [
            'host' => 'minio',
            'port' => 9000,
            'icon' => 'heroicon-o-server',
            'description' => 'Object storage',
        ],
    ];

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getServices(): array
    {
        return collect(self::SERVICES)
            ->map(function (array $service, string $name): array {
                [$online, $latency] = $this->checkService(
                    $service['host'],
                    $service['port'],
                );

                return [
                    'name' => $name,
                    'host' => $service['host'],
                    'port' => $service['port'],
                    'icon' => $service['icon'],
                    'description' => $service['description'],
                    'online' => $online,
                    'latency' => $latency,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array{0: bool, 1: float|null}
     */
    private function checkService(string $host, int $port): array
    {
        $start = microtime(true);

        $socket = @fsockopen($host, $port, $errorCode, $errorMessage, self::TIMEOUT_SECONDS);

        if ($socket === false) {
            return [false, null];
        }

        fclose($socket);

        return [true, round((microtime(true) - $start) * 1000, 1)];
    }
}