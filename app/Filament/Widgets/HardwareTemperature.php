<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;
use Livewire\Attributes\Computed;

class HardwareTemperature extends Widget
{
    private const HWMON_ROOT = '/sys/class/hwmon';

    private const CACHE_KEY = 'widgets.hardware-temperature';

    private const CACHE_TTL_SECONDS = 5;

    private const COLOR_EXCELLENT = '#2dd4bf';

    private const COLOR_GOOD = '#4ade80';

    private const COLOR_WARNING = '#f59e0b';

    private const COLOR_CRITICAL = '#f43f5e';

    private const COLOR_NEUTRAL = '#9ca3af';

    protected string $view = 'filament.widgets.hardware-temperature';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 2;

    #[Computed]
    public function sensors(): array
    {
        $readings = Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, fn () => $this->readings());

        return collect($this->definitions())
            ->map(fn (array $definition, string $key) => $this->present($definition, $readings[$key] ?? null))
            ->all();
    }

    private function definitions(): array
    {
        return [
            'cpu' => [
                'label' => 'CPU',
                'sub' => 'Processore',
                'icon' => 'heroicon-o-cpu-chip',
                'unit' => '°C',
                'thresholds' => [50, 70, 85],
            ],
            'motherboard' => [
                'label' => 'Scheda madre',
                'sub' => 'Chipset',
                'icon' => 'heroicon-o-server-stack',
                'unit' => '°C',
                'thresholds' => [45, 60, 75],
            ],
            'gpu' => [
                'label' => 'GPU',
                'sub' => 'Scheda video',
                'icon' => 'heroicon-o-computer-desktop',
                'unit' => '°C',
                'thresholds' => [55, 75, 88],
            ],
            'nvme' => [
                'label' => 'NVMe',
                'sub' => 'Archiviazione',
                'icon' => 'heroicon-o-circle-stack',
                'unit' => '°C',
                'thresholds' => [45, 60, 70],
            ],
            'fan' => [
                'label' => 'Ventola',
                'sub' => 'Raffreddamento',
                'icon' => 'heroicon-o-arrow-path',
                'unit' => 'RPM',
                'thresholds' => null,
            ],
        ];
    }

    private function readings(): array
    {
        $chips = $this->chips();

        return [
            'cpu' => $this->highest($chips, '/^(coretemp|k10temp)$/', 'temperatures'),
            'motherboard' => $this->highest($chips, '/^(pch_|nct|it87|w83)/', 'temperatures'),
            'gpu' => $this->highest($chips, '/^(amdgpu|nouveau)$/', 'temperatures') ?? $this->nvidiaTemperature(),
            'nvme' => $this->highest($chips, '/^nvme$/', 'temperatures'),
            'fan' => $this->highest($chips, '/./', 'fans'),
        ];
    }

    private function chips(): array
    {
        return collect(glob(self::HWMON_ROOT.'/hwmon*') ?: [])
            ->map(fn (string $path) => [
                'name' => $this->readFile($path.'/name'),
                'temperatures' => $this->readInputs($path.'/temp*_input', 1000),
                'fans' => $this->readInputs($path.'/fan*_input', 1),
            ])
            ->all();
    }

    private function readInputs(string $pattern, int $divisor): array
    {
        return collect(glob($pattern) ?: [])
            ->map(fn (string $file) => $this->readFile($file))
            ->filter(fn (?string $raw) => is_numeric($raw))
            ->map(fn (string $raw) => $raw / $divisor)
            ->values()
            ->all();
    }

    private function readFile(string $path): ?string
    {
        $content = @file_get_contents($path);

        return $content === false ? null : trim($content);
    }

    private function highest(array $chips, string $namePattern, string $kind): ?float
    {
        $values = collect($chips)
            ->filter(fn (array $chip) => $chip['name'] !== null && preg_match($namePattern, $chip['name']))
            ->flatMap(fn (array $chip) => $chip[$kind])
            ->all();

        return $values === [] ? null : (float) max($values);
    }

    private function nvidiaTemperature(): ?float
    {
        $result = Process::timeout(2)->run([
            'nvidia-smi',
            '--query-gpu=temperature.gpu',
            '--format=csv,noheader,nounits',
        ]);

        $firstLine = trim(explode("\n", trim($result->output()))[0]);

        return $result->successful() && is_numeric($firstLine) ? (float) $firstLine : null;
    }

    private function present(array $definition, ?float $value): array
    {
        [$label, $color] = $this->status($definition['thresholds'], $value);

        return $definition + [
            'display' => $value === null ? 'N/D' : (string) round($value, $definition['unit'] === 'RPM' ? 0 : 1),
            'status_label' => $label,
            'status_color' => $color,
        ];
    }

    private function status(?array $thresholds, ?float $value): array
    {
        return match (true) {
            $value === null => ['N/D', self::COLOR_NEUTRAL],
            $thresholds === null => $value > 0 ? ['Attiva', self::COLOR_GOOD] : ['Spenta', self::COLOR_NEUTRAL],
            $value < $thresholds[0] => ['Ottima', self::COLOR_EXCELLENT],
            $value < $thresholds[1] => ['Buona', self::COLOR_GOOD],
            $value < $thresholds[2] => ['Attenzione', self::COLOR_WARNING],
            default => ['Critica', self::COLOR_CRITICAL],
        };
    }
}