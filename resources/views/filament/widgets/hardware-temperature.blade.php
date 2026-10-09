<x-filament-widgets::widget>
    <x-filament::section
        heading="Temperature"
        description="Monitoraggio hardware"
        icon="heroicon-o-cpu-chip"
    >
        <div class="temperature-widget">
            @foreach ($this->getSensors() as $sensor)
                <div class="temperature-item">
                    <div class="temperature-icon" style="color: {{ $sensor['status_color'] }}; background: color-mix(in srgb, {{ $sensor['status_color'] }} 12%, transparent)">
                        <x-filament::icon :icon="$sensor['icon']" />
                    </div>

                    <div class="temperature-info">
                        <div class="temperature-name">{{ $sensor['label'] }}</div>
                        <div class="temperature-description">{{ $sensor['sub'] }}</div>
                    </div>

                    <div class="temperature-value">
                        <div class="temperature-number">
                            @if ($sensor['value'] !== null)
                                {{ rtrim(rtrim(number_format((float) $sensor['value'], 1, '.', ''), '0'), '.') }}<span class="temperature-unit"> {{ $sensor['unit'] }}</span>
                            @else
                                N/D
                            @endif
                        </div>
                        <div class="temperature-status" style="color: {{ $sensor['status_color'] }}">
                            {{ $sensor['status_label'] }}
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="temperature-footer">
                <span>Ultimo controllo</span>
                <div class="temperature-live">
                    <span class="temperature-live-dot"></span>
                    <span>{{ now()->format('H:i:s') }}</span>
                </div>
            </div>
        </div>
    </x-filament::section>

    <style>
        .temperature-widget{width:100%}
        .temperature-widget .temperature-item{display:flex;align-items:center;gap:12px;min-height:54px;padding:9px 4px;border-top:1px solid rgba(0,0,0,.05);transition:background-color 150ms ease}
        .temperature-widget .temperature-item:first-child{border-top:0}
        .temperature-widget .temperature-item:hover{background:rgba(0,0,0,.018)}
        .temperature-widget .temperature-icon{width:32px;height:32px;flex:0 0 32px;display:flex;align-items:center;justify-content:center;border-radius:9px}
        .temperature-widget .temperature-icon svg{width:16px;height:16px}
        .temperature-widget .temperature-info{min-width:0;flex:1}
        .temperature-widget .temperature-name{color:rgb(17,24,39);font-size:13px;font-weight:500;line-height:18px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .temperature-widget .temperature-description{margin-top:2px;color:rgb(156,163,175);font-size:11px;line-height:15px}
        .temperature-widget .temperature-value{flex:0 0 auto;min-width:58px;text-align:right}
        .temperature-widget .temperature-number{color:rgb(17,24,39);font-size:14px;font-weight:600;line-height:18px;font-variant-numeric:tabular-nums;white-space:nowrap}
        .temperature-widget .temperature-unit{color:rgb(156,163,175);font-size:10px;font-weight:400}
        .temperature-widget .temperature-status{margin-top:2px;font-size:9px;font-weight:500;line-height:13px}
        .temperature-widget .temperature-footer{display:flex;align-items:center;justify-content:space-between;margin-top:8px;padding:10px 4px 0;border-top:1px solid rgba(0,0,0,.05);color:rgb(156,163,175);font-size:10px;line-height:14px}
        .temperature-widget .temperature-live{display:flex;align-items:center;gap:6px;color:rgb(107,114,128);font-weight:500}
        .temperature-widget .temperature-live-dot{width:6px;height:6px;display:block;border-radius:50%;background:rgb(34,197,94);box-shadow:0 0 0 3px rgba(34,197,94,.10)}
        .dark .temperature-widget .temperature-item{border-color:rgba(255,255,255,.05)}
        .dark .temperature-widget .temperature-item:hover{background:rgba(255,255,255,.025)}
        .dark .temperature-widget .temperature-name,.dark .temperature-widget .temperature-number{color:rgb(255,255,255)}
        .dark .temperature-widget .temperature-footer{border-color:rgba(255,255,255,.05)}
        .dark .temperature-widget .temperature-live{color:rgb(156,163,175)}
    </style>
</x-filament-widgets::widget>
