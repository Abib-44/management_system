<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
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
            ->map(fn (array $definition, string $key) => $this->present($definition, $readings[$key]))
            ->all();
    }

    private function definitions(): array
    {
        $voltages = [];
        foreach (range(0, 3) as $i) {
            $voltages['voltage_'.$i] = [
                'label' => 'Tensione '.($i + 1),
                'sub' => 'Alimentazione (in'.$i.')',
                'icon' => 'heroicon-o-bolt',
                'unit' => 'V',
                'chip' => '/^ftsteutates$/',
                'kind' => 'in',
                'index' => $i,
                'thresholds' => null,
                'range' => [2.7, 3.6],
            ];
        }

        return [
            'cpu' => [
                'label' => 'CPU',
                'sub' => 'Processore',
                'icon' => 'heroicon-o-cpu-chip',
                'unit' => '°C',
                'chip' => '/^(coretemp|k10temp)$/',
                'kind' => 'temp',
                'thresholds' => [55, 70, 85],
            ],
            'motherboard' => [
                'label' => 'Scheda madre',
                'sub' => 'Chipset',
                'icon' => 'heroicon-o-server-stack',
                'unit' => '°C',
                'chip' => '/^(pch_|thinkpad|nct|it87|w83|acpitz)/',
                'kind' => 'temp',
                'thresholds' => [50, 65, 80],
            ],
            'system' => [
                'label' => 'Sistema',
                'sub' => 'Sensori Fujitsu',
                'icon' => 'heroicon-o-fire',
                'unit' => '°C',
                'chip' => '/^ftsteutates$/',
                'kind' => 'temp',
                'thresholds' => [55, 70, 85],
            ],
            'gpu' => [
                'label' => 'GPU',
                'sub' => 'Scheda video',
                'icon' => 'heroicon-o-computer-desktop',
                'unit' => '°C',
                'chip' => '/^(amdgpu|nouveau|radeon)$/',
                'kind' => 'temp',
                'thresholds' => [55, 75, 88],
            ],
            'nvme' => [
                'label' => 'NVMe',
                'sub' => 'Archiviazione',
                'icon' => 'heroicon-o-circle-stack',
                'unit' => '°C',
                'chip' => '/^(nvme|drivetemp)$/',
                'kind' => 'temp',
                'thresholds' => [45, 60, 70],
            ],
            'fan' => [
                'label' => 'Ventola',
                'sub' => 'Raffreddamento',
                'icon' => 'heroicon-o-arrow-path',
                'unit' => 'RPM',
                'chip' => '/./',
                'kind' => 'fan',
                'thresholds' => null,
            ],
        ] + $voltages;
    }

    private function readings(): array
    {
        $chips = $this->chips();

        return collect($this->definitions())
            ->map(fn (array $definition) => $this->highest(
                $chips,
                $definition['chip'],
                $definition['kind'],
                $definition['index'] ?? null,
            ))
            ->all();
    }

    private function chips(): Collection
    {
        return collect(glob(self::HWMON_ROOT.'/hwmon*') ?: [])
            ->map(fn (string $path) => [
                'name' => $this->readFile($path.'/name') ?? basename($path),
                'temp' => $this->readInputs($path.'/temp*_input', 1000)->filter(fn (float $value) => $value > 0),
                'fan' => $this->readInputs($path.'/fan*_input', 1),
                'in' => $this->readInputs($path.'/in*_input', 1000),
            ]);
    }

    private function readInputs(string $pattern, int $divisor): Collection
    {
        return collect(glob($pattern) ?: [])
            ->map(fn (string $file) => $this->readFile($file))
            ->filter(fn (?string $raw) => is_numeric($raw))
            ->map(fn (string $raw) => $raw / $divisor)
            ->values();
    }

    private function readFile(string $path): ?string
    {
        $content = @file_get_contents($path);

        return $content === false ? null : trim($content);
    }

    private function highest(Collection $chips, string $namePattern, string $kind, ?int $index = null): ?float
    {
        $values = $chips
            ->filter(fn (array $chip) => preg_match($namePattern, $chip['name']))
            ->flatMap(fn (array $chip) => $chip[$kind])
            ->values();

        if ($index !== null) {
            $value = $values->get($index);

            return $value === null ? null : (float) $value;
        }

        return $values->isEmpty() ? null : (float) $values->max();
    }

    private function present(array $definition, ?float $value): array
    {
        [$label, $color] = $this->status($definition, $value);

        $decimals = match ($definition['unit']) {
            'RPM' => 0,
            'V' => 2,
            default => 1,
        };

        return $definition + [
            'display' => $value === null ? 'N/D' : (string) round($value, $decimals),
            'status_label' => $label,
            'status_color' => $color,
        ];
    }

    private function status(array $definition, ?float $value): array
    {
        $thresholds = $definition['thresholds'];
        $range = $definition['range'] ?? null;

        return match (true) {
            $value === null => ['N/D', self::COLOR_NEUTRAL],
            $range !== null => ($value >= $range[0] && $value <= $range[1])
                ? ['Stabile', self::COLOR_GOOD]
                : ['Fuori range', self::COLOR_CRITICAL],
            $thresholds === null => $value > 0 ? ['Attiva', self::COLOR_GOOD] : ['Spenta', self::COLOR_NEUTRAL],
            $value < $thresholds[0] => ['Ottima', self::COLOR_EXCELLENT],
            $value < $thresholds[1] => ['Buona', self::COLOR_GOOD],
            $value < $thresholds[2] => ['Attenzione', self::COLOR_WARNING],
            default => ['Critica', self::COLOR_CRITICAL],
        };
    }
}