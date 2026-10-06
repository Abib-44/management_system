<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class HardwareTemperature extends Widget
{
    protected string $view = 'filament.widgets.hardware-temperature';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 2;

    /**
     * Dati grezzi per ogni sensore: valore reale, max di riferimento, unità.
     * Il "max" è la soglia che consideri critica per quel componente,
     * non un limite fisico assoluto: adattala al tuo hardware.
     */
    protected function getRawSensors(): array
    {
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
                'sub' => 'Motherboard',
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
                'value' => $this->readFanRpm(),
                'max' => 2000,
                'unit' => 'RPM',
                'inactive' => $this->readFanRpm() === 0,
            ],
        ];
    }

    /**
     * Punto d'ingresso per la view: aggiunge pct, status e colore già calcolati.
     */
    public function getSensors(): array
    {
        $sensors = [];

        foreach ($this->getRawSensors() as $key => $s) {
            $pct = $s['value'] !== null
                ? (int) min(100, round(($s['value'] / $s['max']) * 100))
                : 0;

            [$statusLabel, $statusColor] = $this->statusFor($pct, ! empty($s['inactive']), $s['value'] === null);

            $sensors[$key] = array_merge($s, [
                'pct' => $pct,
                'status_label' => $statusLabel,
                'status_color' => $statusColor,
            ]);
        }

        return $sensors;
    }

    /**
     * Media ponderata: percentuale rispetto alla soglia critica di ogni sensore,
     * non media aritmetica dei gradi (che mischierebbe scale diverse).
     * La ventola (RPM) è esclusa perché non è una temperatura.
     */
    public function getOverallLoad(): ?int
    {
        $percentages = [];

        foreach ($this->getRawSensors() as $key => $s) {
            if ($key === 'fan' || $s['value'] === null) {
                continue;
            }

            $percentages[] = min(100, ($s['value'] / $s['max']) * 100);
        }

        if (empty($percentages)) {
            return null;
        }

        return (int) round(array_sum($percentages) / count($percentages));
    }

    protected function statusFor(int $pct, bool $inactive, bool $unavailable): array
    {
        if ($unavailable) {
            return ['N/D', '#9ca3af'];
        }
        if ($inactive) {
            return ['Spenta', '#9ca3af'];
        }
        if ($pct < 35) {
            return ['Ottima', '#2dd4bf'];
        }
        if ($pct < 60) {
            return ['Buona', '#4ade80'];
        }
        if ($pct < 82) {
            return ['Attenzione', '#f59e0b'];
        }

        return ['Critica', '#f43f5e'];
    }

    /**
     * Legge la temperatura CPU da `sensors -j` (pacchetto lm-sensors).
     * Cerca il primo chip che contiene "Package" o "Tdie"/"Tctl" (Intel/AMD).
     * Adatta le chiavi ai nomi che vedi tu con `sensors -j` da terminale.
     */
    protected function readCpuTemp(): ?float
    {
        $data = $this->readSensorsJson();

        foreach ($data as $chip) {
            foreach ($chip as $key => $value) {
                if (! is_array($value)) {
                    continue;
                }
                if (str_contains($key, 'Package') || str_contains($key, 'Tdie') || str_contains($key, 'Tctl')) {
                    foreach ($value as $subKey => $subValue) {
                        if (str_starts_with($subKey, 'temp') && str_ends_with($subKey, '_input')) {
                            return (float) $subValue;
                        }
                    }
                }
            }
        }

        return null;
    }

    protected function readMotherboardTemp(): ?float
    {
        $data = $this->readSensorsJson();

        // Nomi tipici dei chip Super I/O sulla scheda madre: nct6775, it87, w83627ehf...
        foreach ($data as $chipName => $chip) {
            if (preg_match('/nct|it87|w83|f71|lm(78|87|85)/i', $chipName)) {
                foreach ($chip as $key => $value) {
                    if (! is_array($value) || ! str_contains($key, 'temp1')) {
                        continue;
                    }
                    foreach ($value as $subKey => $subValue) {
                        if (str_ends_with($subKey, '_input')) {
                            return (float) $subValue;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * GPU NVIDIA via nvidia-smi. Per AMD/Intel integrata la fonte cambia
     * (es. amdgpu appare già dentro `sensors -j`).
     */
    protected function readGpuTemp(): ?float
    {
        $output = @shell_exec('nvidia-smi --query-gpu=temperature.gpu --format=csv,noheader,nounits 2>/dev/null');

        if ($output !== null && trim($output) !== '') {
            return (float) trim($output);
        }

        return null;
    }

    /**
     * Temperatura NVMe via smartctl (richiede smartmontools installato).
     */
    protected function readNvmeTemp(): ?float
    {
        $output = @shell_exec('smartctl -A /dev/nvme0 2>/dev/null | grep -i temperature');

        if ($output && preg_match('/(\d+)\s*Celsius/', $output, $matches)) {
            return (float) $matches[1];
        }

        return null;
    }

    protected function readFanRpm(): int
    {
        $data = $this->readSensorsJson();

        foreach ($data as $chip) {
            foreach ($chip as $key => $value) {
                if (! is_array($value) || ! str_starts_with($key, 'fan')) {
                    continue;
                }
                foreach ($value as $subKey => $subValue) {
                    if (str_ends_with($subKey, '_input')) {
                        return (int) $subValue;
                    }
                }
            }
        }

        return 0;
    }

    protected function readSensorsJson(): array
    {
        $output = @shell_exec('sensors -j 2>/dev/null');

        if (! $output) {
            return [];
        }

        $decoded = json_decode($output, true);

        return is_array($decoded) ? $decoded : [];
    }
}
