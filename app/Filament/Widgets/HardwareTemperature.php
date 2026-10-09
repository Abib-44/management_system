<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class HardwareTemperature extends Widget
{
    protected string $view = 'filament.widgets.hardware-temperature';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 2;

    protected function getRawSensors(): array
    {
        $fanRpm = $this->readFanRpm();

        return [
            'cpu' => [
                'label' => 'CPU',
                'sub' => 'Processore',
                'icon' => 'heroicon-o-cpu-chip',
                'value' => $this->readCpuTemp(),
                'max' => 90,
                'unit' => '°C',
            ],
            'motherboard' => [
                'label' => 'Scheda madre',
                'sub' => 'Sensore PCH',
                'icon' => 'heroicon-o-server-stack',
                'value' => $this->readMotherboardTemp(),
                'max' => 80,
                'unit' => '°C',
            ],
            'gpu' => [
                'label' => 'GPU',
                'sub' => 'Scheda video',
                'icon' => 'heroicon-o-computer-desktop',
                'value' => $this->readGpuTemp(),
                'max' => 90,
                'unit' => '°C',
            ],
            'nvme' => [
                'label' => 'NVMe',
                'sub' => 'Archiviazione',
                'icon' => 'heroicon-o-circle-stack',
                'value' => $this->readNvmeTemp(),
                'max' => 70,
                'unit' => '°C',
            ],
            'fan' => [
                'label' => 'Ventola',
                'sub' => 'Raffreddamento',
                'icon' => 'heroicon-o-arrow-path',
                'value' => $fanRpm,
                'max' => 2000,
                'unit' => 'RPM',
                'inactive' => $fanRpm === 0,
            ],
        ];
    }

    public function getSensors(): array
    {
        $sensors = [];

        foreach ($this->getRawSensors() as $key => $sensor) {
            $value = $sensor['value'];
            $percentage = $value !== null
                ? (int) min(100, round(($value / $sensor['max']) * 100))
                : 0;

            [$statusLabel, $statusColor] = $this->statusFor(
                $percentage,
                (bool) ($sensor['inactive'] ?? false),
                $value === null,
            );

            $sensors[$key] = array_merge($sensor, [
                'pct' => $percentage,
                'status_label' => $statusLabel,
                'status_color' => $statusColor,
            ]);
        }

        return $sensors;
    }

    public function getOverallLoad(): ?int
    {
        $percentages = [];

        foreach ($this->getRawSensors() as $key => $sensor) {
            if ($key === 'fan' || $sensor['value'] === null) {
                continue;
            }

            $percentages[] = min(100, ($sensor['value'] / $sensor['max']) * 100);
        }

        return $percentages === []
            ? null
            : (int) round(array_sum($percentages) / count($percentages));
    }

    protected function statusFor(int $percentage, bool $inactive, bool $unavailable): array
    {
        if ($unavailable) {
            return ['N/D', '#9ca3af'];
        }

        if ($inactive) {
            return ['Spenta', '#9ca3af'];
        }

        if ($percentage < 35) {
            return ['Ottima', '#2dd4bf'];
        }

        if ($percentage < 60) {
            return ['Buona', '#4ade80'];
        }

        if ($percentage < 82) {
            return ['Attenzione', '#f59e0b'];
        }

        return ['Critica', '#f43f5e'];
    }

    protected function readCpuTemp(): ?float
    {
        $data = $this->readSensorsJson();

        foreach ($data as $chip) {
            foreach ($chip as $sensorName => $sensorData) {
                if (! is_array($sensorData)) {
                    continue;
                }

                if (! preg_match('/package|tdie|tctl/i', $sensorName)) {
                    continue;
                }

                foreach ($sensorData as $field => $value) {
                    if (preg_match('/^temp\d+_input$/', $field) && is_numeric($value)) {
                        return (float) $value;
                    }
                }
            }
        }

        return null;
    }

    protected function readMotherboardTemp(): ?float
    {
        $data = $this->readSensorsJson();

        foreach ($data as $chipName => $chip) {
            if (! preg_match('/pch|nct|it87|w83|f71|lm(78|87|85)/i', $chipName)) {
                continue;
            }

            foreach ($chip as $sensorName => $sensorData) {
                if (! is_array($sensorData) || ! preg_match('/temp\d+/', $sensorName)) {
                    continue;
                }

                foreach ($sensorData as $field => $value) {
                    if (preg_match('/^temp\d+_input$/', $field) && is_numeric($value)) {
                        return (float) $value;
                    }
                }
            }
        }

        return null;
    }

    protected function readGpuTemp(): ?float
    {
        $output = $this->runCommand(['nvidia-smi', '--query-gpu=temperature.gpu', '--format=csv,noheader,nounits']);

        if ($output === null || ! is_numeric(trim($output))) {
            return null;
        }

        return (float) trim($output);
    }

    protected function readNvmeTemp(): ?float
    {
        foreach (['/dev/nvme0', '/dev/nvme0n1'] as $device) {
            $output = $this->runCommand(['smartctl', '-A', $device]);

            if ($output !== null && preg_match('/Temperature:\s*(\d+(?:\.\d+)?)\s*(?:Celsius|°C)/i', $output, $matches)) {
                return (float) $matches[1];
            }

            if ($output !== null && preg_match('/Temperature:\s*(\d+(?:\.\d+)?)/i', $output, $matches)) {
                return (float) $matches[1];
            }
        }

        return null;
    }

    protected function readFanRpm(): ?int
    {
        foreach ($this->readSensorsJson() as $chip) {
            foreach ($chip as $sensorData) {
                if (! is_array($sensorData)) {
                    continue;
                }

                foreach ($sensorData as $field => $value) {
                    if (preg_match('/^fan\d+_input$/', $field) && is_numeric($value)) {
                        return (int) $value;
                    }
                }
            }
        }

        return null;
    }

    protected function readSensorsJson(): array
    {
        $output = $this->runCommand(['sensors', '-j']);

        if ($output === null) {
            return [];
        }

        $decoded = json_decode($output, true);

        return is_array($decoded) ? $decoded : [];
    }

    protected function runCommand(array $command): ?string
    {
        if (! function_exists('exec')) {
            return null;
        }

        $escapedCommand = implode(' ', array_map('escapeshellarg', $command));
        $output = [];
        $exitCode = 0;

        exec($escapedCommand . ' 2>/dev/null', $output, $exitCode);

        if ($exitCode !== 0) {
            return null;
        }

        return implode("\n", $output);
    }
}
