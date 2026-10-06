<x-filament-widgets::widget>

    <style>
        .service-status {
            width: 100%;
        }

        .service-status__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .service-status__title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.01em;
        }

        .service-status__subtitle {
            margin-top: 0.25rem;
            font-size: 0.8rem;
            color: #6b7280;
        }

        .service-status__live {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.7rem;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            background: #ffffff;
            color: #6b7280;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .service-status__live-dot {
            position: relative;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
        }

        .service-status__live-dot::after {
            content: "";
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background: #22c55e;
            opacity: 0.2;
            animation: service-pulse 2s infinite;
        }

        .service-status__grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .service-card {
            position: relative;
            min-width: 0;
            padding: 1.25rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.9rem;
            background: #ffffff;
            box-shadow:
                0 1px 2px rgba(0, 0, 0, 0.03),
                0 4px 12px rgba(0, 0, 0, 0.025);
            transition:
                transform 180ms ease,
                box-shadow 180ms ease,
                border-color 180ms ease;
        }

        .service-card:hover {
            transform: translateY(-2px);
            border-color: #d1d5db;
            box-shadow:
                0 4px 8px rgba(0, 0, 0, 0.04),
                0 12px 24px rgba(0, 0, 0, 0.06);
        }

        .service-card__top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .service-card__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.6rem;
            height: 2.6rem;
            border-radius: 0.7rem;
        }

        .service-card--online .service-card__icon {
            background: #f0fdf4;
            color: #16a34a;
        }

        .service-card--offline .service-card__icon {
            background: #fef2f2;
            color: #dc2626;
        }

        .service-card__icon svg {
            width: 1.3rem;
            height: 1.3rem;
        }

        .service-card__indicator {
            width: 8px;
            height: 8px;
            margin-top: 0.25rem;
            border-radius: 50%;
        }

        .service-card--online .service-card__indicator {
            background: #22c55e;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12);
        }

        .service-card--offline .service-card__indicator {
            background: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12);
        }

        .service-card__name {
            margin-top: 1rem;
            font-size: 0.95rem;
            font-weight: 700;
            color: #111827;
        }

        .service-card__description {
            margin-top: 0.2rem;
            font-size: 0.72rem;
            color: #9ca3af;
        }

        .service-card__status {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1.25rem;
        }

        .service-card__state {
            font-size: 0.78rem;
            font-weight: 700;
        }

        .service-card--online .service-card__state {
            color: #16a34a;
        }

        .service-card--offline .service-card__state {
            color: #dc2626;
        }

        .service-card__endpoint {
            margin-top: 0.25rem;
            color: #9ca3af;
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;
            font-size: 0.65rem;
        }

        .service-card__latency {
            text-align: right;
        }

        .service-card__latency-value {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
        }

        .service-card__latency-unit {
            margin-left: 2px;
            font-size: 0.65rem;
            font-weight: 500;
            color: #9ca3af;
        }

        .service-card__latency-label {
            margin-top: 1px;
            font-size: 0.6rem;
            color: #9ca3af;
        }

        @keyframes service-pulse {
            0%,
            100% {
                transform: scale(1);
                opacity: 0.2;
            }

            50% {
                transform: scale(1.6);
                opacity: 0;
            }
        }

        @media (max-width: 1200px) {
            .service-status__grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .service-status__header {
                align-items: flex-start;
                flex-direction: column;
            }

            .service-status__grid {
                grid-template-columns: 1fr;
            }
        }

        @media (prefers-color-scheme: dark) {
            .service-status__title,
            .service-card__name,
            .service-card__latency-value {
                color: #f9fafb;
            }

            .service-status__subtitle,
            .service-card__description,
            .service-card__endpoint,
            .service-card__latency-unit,
            .service-card__latency-label {
                color: #9ca3af;
            }

            .service-status__live,
            .service-card {
                border-color: rgba(255, 255, 255, 0.1);
                background: rgba(255, 255, 255, 0.03);
            }

            .service-status__live {
                color: #9ca3af;
            }

            .service-card:hover {
                border-color: rgba(255, 255, 255, 0.16);
            }

            .service-card--online .service-card__icon {
                background: rgba(34, 197, 94, 0.1);
            }

            .service-card--offline .service-card__icon {
                background: rgba(239, 68, 68, 0.1);
            }
        }
    </style>

    <div class="service-status">

        {{-- Header --}}
        <div class="service-status__header">

            <div>
                <h2 class="service-status__title">
                    System Status
                </h2>

                <div class="service-status__subtitle">
                    Stato dei servizi e dell'infrastruttura
                </div>
            </div>

            <div class="service-status__live">
                <span class="service-status__live-dot"></span>
                Live monitoring
            </div>

        </div>

        {{-- Services --}}
        <div class="service-status__grid">

            @foreach ($this->getServices() as $service)

                <div @class([
                    'service-card',
                    'service-card--online' => $service['online'],
                    'service-card--offline' => ! $service['online'],
                ])>

                    {{-- Icon + status --}}
                    <div class="service-card__top">

                        <div class="service-card__icon">
                            <x-dynamic-component
                                :component="$service['icon']"
                            />
                        </div>

                        <div class="service-card__indicator"></div>

                    </div>

                    {{-- Service name --}}
                    <div class="service-card__name">
                        {{ $service['name'] }}
                    </div>

                    {{-- Description --}}
                    <div class="service-card__description">
                        {{ $service['description'] }}
                    </div>

                    {{-- Status + latency --}}
                    <div class="service-card__status">

                        <div>

                            <div class="service-card__state">
                                {{ $service['online'] ? 'Online' : 'Offline' }}
                            </div>

                            <div class="service-card__endpoint">
                                {{ $service['host'] }}:{{ $service['port'] }}
                            </div>

                        </div>

                        @if ($service['online'])

                            <div class="service-card__latency">

                                <div class="service-card__latency-value">
                                    {{ $service['latency'] }}
                                    <span class="service-card__latency-unit">
                                        ms
                                    </span>
                                </div>

                                <div class="service-card__latency-label">
                                    response
                                </div>

                            </div>

                        @else

                            <div class="service-card__latency">

                                <div class="service-card__latency-value">
                                    —
                                </div>

                                <div class="service-card__latency-label">
                                    unavailable
                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</x-filament-widgets::widget>