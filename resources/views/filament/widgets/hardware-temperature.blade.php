```blade
<x-filament-widgets::widget>

    <style>
        /* ================================
           TEMPERATURE WIDGET
           ================================ */

        .temperature-widget {
            width: 100%;
        }

        /* ITEM */

        .temperature-widget .temperature-item {
            display: flex;
            align-items: center;
            gap: 12px;

            min-height: 54px;
            padding: 9px 4px;

            border-top: 1px solid rgba(0, 0, 0, 0.05);

            transition: background-color 150ms ease;
        }

        .temperature-widget .temperature-item:first-child {
            border-top: 0;
        }

        .temperature-widget .temperature-item:hover {
            background: rgba(0, 0, 0, 0.018);
        }


        /* ICON */

        .temperature-widget .temperature-icon {
            width: 32px;
            height: 32px;

            flex: 0 0 32px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 9px;
        }

        .temperature-widget .temperature-icon svg {
            width: 16px;
            height: 16px;
        }

        .temperature-widget .temperature-icon-success {
            color: rgb(34, 197, 94);
            background: rgba(34, 197, 94, 0.10);
        }

        .temperature-widget .temperature-icon-primary {
            color: rgb(99, 102, 241);
            background: rgba(99, 102, 241, 0.10);
        }

        .temperature-widget .temperature-icon-info {
            color: rgb(6, 182, 212);
            background: rgba(6, 182, 212, 0.10);
        }

        .temperature-widget .temperature-icon-warning {
            color: rgb(245, 158, 11);
            background: rgba(245, 158, 11, 0.10);
        }

        .temperature-widget .temperature-icon-gray {
            color: rgb(107, 114, 128);
            background: rgba(107, 114, 128, 0.10);
        }


        /* INFORMATION */

        .temperature-widget .temperature-info {
            min-width: 0;
            flex: 1;
        }

        .temperature-widget .temperature-name {
            color: rgb(17, 24, 39);

            font-size: 13px;
            font-weight: 500;
            line-height: 18px;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .temperature-widget .temperature-description {
            margin-top: 2px;

            color: rgb(156, 163, 175);

            font-size: 11px;
            line-height: 15px;
        }


        /* VALUE */

        .temperature-widget .temperature-value {
            flex: 0 0 auto;
            min-width: 58px;

            text-align: right;
        }

        .temperature-widget .temperature-number {
            color: rgb(17, 24, 39);

            font-size: 14px;
            font-weight: 600;
            line-height: 18px;

            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .temperature-widget .temperature-unit {
            color: rgb(156, 163, 175);

            font-size: 10px;
            font-weight: 400;
        }


        /* STATUS */

        .temperature-widget .temperature-status {
            margin-top: 2px;

            font-size: 9px;
            font-weight: 500;
            line-height: 13px;
        }

        .temperature-widget .status-success {
            color: rgb(34, 197, 94);
        }

        .temperature-widget .status-gray {
            color: rgb(156, 163, 175);
        }


        /* FOOTER */

        .temperature-widget .temperature-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-top: 8px;
            padding: 10px 4px 0;

            border-top: 1px solid rgba(0, 0, 0, 0.05);

            color: rgb(156, 163, 175);

            font-size: 10px;
            line-height: 14px;
        }

        .temperature-widget .temperature-live {
            display: flex;
            align-items: center;
            gap: 6px;

            color: rgb(107, 114, 128);
            font-weight: 500;
        }

        .temperature-widget .temperature-live-dot {
            width: 6px;
            height: 6px;

            display: block;

            border-radius: 50%;

            background: rgb(34, 197, 94);

            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.10);
        }


        /* DARK MODE */

        .dark .temperature-widget .temperature-item {
            border-color: rgba(255, 255, 255, 0.05);
        }

        .dark .temperature-widget .temperature-item:hover {
            background: rgba(255, 255, 255, 0.025);
        }

        .dark .temperature-widget .temperature-name {
            color: rgb(255, 255, 255);
        }

        .dark .temperature-widget .temperature-number {
            color: rgb(255, 255, 255);
        }

        .dark .temperature-widget .temperature-footer {
            border-color: rgba(255, 255, 255, 0.05);
        }

        .dark .temperature-widget .temperature-live {
            color: rgb(156, 163, 175);
        }
    </style>


    <x-filament::section
        heading="Temperature"
        description="Monitoraggio hardware"
        icon="heroicon-o-cpu-chip"
    >

        <div class="temperature-widget">

            {{-- CPU --}}
            <div class="temperature-item">

                <div class="temperature-icon temperature-icon-success">
                    <x-filament::icon
                        icon="heroicon-o-cpu-chip"
                    />
                </div>

                <div class="temperature-info">
                    <div class="temperature-name">
                        CPU
                    </div>

                    <div class="temperature-description">
                        Processore
                    </div>
                </div>

                <div class="temperature-value">
                    <div class="temperature-number">
                        38°C
                    </div>

                    <div class="temperature-status status-success">
                        Ottima
                    </div>
                </div>

            </div>


            {{-- SCHEDA MADRE --}}
            <div class="temperature-item">

                <div class="temperature-icon temperature-icon-primary">
                    <x-filament::icon
                        icon="heroicon-o-server-stack"
                    />
                </div>

                <div class="temperature-info">
                    <div class="temperature-name">
                        Scheda madre
                    </div>

                    <div class="temperature-description">
                        Motherboard
                    </div>
                </div>

                <div class="temperature-value">
                    <div class="temperature-number">
                        37°C
                    </div>

                    <div class="temperature-status status-success">
                        Buona
                    </div>
                </div>

            </div>


            {{-- GPU --}}
            <div class="temperature-item">

                <div class="temperature-icon temperature-icon-info">
                    <x-filament::icon
                        icon="heroicon-o-computer-desktop"
                    />
                </div>

                <div class="temperature-info">
                    <div class="temperature-name">
                        GPU
                    </div>

                    <div class="temperature-description">
                        Scheda video
                    </div>
                </div>

                <div class="temperature-value">
                    <div class="temperature-number">
                        36°C
                    </div>

                    <div class="temperature-status status-success">
                        Ottima
                    </div>
                </div>

            </div>


            {{-- NVME --}}
            <div class="temperature-item">

                <div class="temperature-icon temperature-icon-warning">
                    <x-filament::icon
                        icon="heroicon-o-circle-stack"
                    />
                </div>

                <div class="temperature-info">
                    <div class="temperature-name">
                        NVMe
                    </div>

                    <div class="temperature-description">
                        Archiviazione
                    </div>
                </div>

                <div class="temperature-value">
                    <div class="temperature-number">
                        34°C
                    </div>

                    <div class="temperature-status status-success">
                        Ottima
                    </div>
                </div>

            </div>


            {{-- VENTOLA --}}
            <div class="temperature-item">

                <div class="temperature-icon temperature-icon-gray">
                    <x-filament::icon
                        icon="heroicon-o-arrow-path"
                    />
                </div>

                <div class="temperature-info">
                    <div class="temperature-name">
                        Ventola
                    </div>

                    <div class="temperature-description">
                        Raffreddamento
                    </div>
                </div>

                <div class="temperature-value">
                    <div class="temperature-number">
                        0
                        <span class="temperature-unit">
                            RPM
                        </span>
                    </div>

                    <div class="temperature-status status-gray">
                        Spenta
                    </div>
                </div>

            </div>


            {{-- FOOTER --}}
            <div class="temperature-footer">

                <span>
                    Ultimo controllo
                </span>

                <div class="temperature-live">
                    <span class="temperature-live-dot"></span>

                    <span>
                        Ora
                    </span>
                </div>

            </div>

        </div>

    </x-filament::section>

</x-filament-widgets::widget>
```
