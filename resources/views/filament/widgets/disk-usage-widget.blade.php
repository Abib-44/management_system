<x-filament-widgets::widget>

    @php
        $disk = $this->getDiskData();

        $percent = min(
            100,
            max(0, (float) ($disk['percent'] ?? 0))
        );

        $used = $disk['used'] ?? '0 GB';
        $total = $disk['total'] ?? '0 GB';

        $free = $disk['free'] ?? null;

        if ($free === null) {
            $usedBytes = (float) ($disk['used_bytes'] ?? 0);
            $totalBytes = (float) ($disk['total_bytes'] ?? 0);

            if ($totalBytes > 0) {
                $freeBytes = max(
                    0,
                    $totalBytes - $usedBytes
                );

                $free = $freeBytes >= 1024 ** 3
                    ? number_format(
                        $freeBytes / 1024 ** 3,
                        2
                    ) . ' GB'
                    : number_format(
                        $freeBytes / 1024 ** 2,
                        0
                    ) . ' MB';
            } else {
                $free = '—';
            }
        }

        if ($percent < 60) {
            $color = '#00e5a0';
        } elseif ($percent < 80) {
            $color = '#ffb020';
        } else {
            $color = '#ff4d6d';
        }

        [$r, $g, $b] = sscanf(
            $color,
            '#%02x%02x%02x'
        );
    @endphp

    <style>
        .storage-section {
            width: 100%;
            height: 100% !important;
            max-height: 100% !important;
            min-height: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden;
        }

        .storage-section .fi-section-content {
            padding: 5px !important;
            height: 100% !important;
            min-height: 0 !important;
            flex: 1 1 auto !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .storage-section > div {
            min-height: 0 !important;
            flex: 1 1 auto !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .storage-section > div > .storage-widget {
            height: 100% !important;
            max-height: 100% !important;
            min-height: 0 !important;
            flex: 1 1 auto !important;
        }

        .storage-widget {
            width: 100%;
            height: 100% !important;
            max-height: 100% !important;
            min-height: 0 !important;
            box-sizing: border-box;
            display: grid;
            grid-template-rows: minmax(0, 1fr) auto;
            overflow: hidden;
            --storage-circumference: 741.42;
        }

        .storage-widget .storage-main {
            width: 100%;
            height: 100%;
            min-height: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .storage-widget .storage-gauge {
            position: relative;
            width: min(100%, 230px);
            aspect-ratio: 1 / 1;
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .storage-widget .storage-gauge-svg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            transform: rotate(-225deg);
            overflow: visible;
        }

        .storage-widget .storage-gauge-track,
        .storage-widget .storage-gauge-progress {
            fill: none;
            stroke-width: 13;
            stroke-linecap: round;
            stroke-dasharray:
                calc(
                    var(--storage-circumference) * .74
                )
                var(--storage-circumference);
        }

        .storage-widget .storage-gauge-track {
            stroke: rgba(127, 127, 127, .11);
        }

        .dark .storage-widget .storage-gauge-track {
            stroke: rgba(255, 255, 255, .09);
        }

        .storage-widget .storage-gauge-progress {
            stroke: var(--storage-color);
            stroke-dashoffset:
                calc(
                    var(--storage-circumference) * .74
                );
            filter:
                drop-shadow(
                    0 0 7px
                    rgba(
                        var(--storage-color-rgb),
                        .68
                    )
                );
        }

        .storage-widget .storage-center {
            position: absolute;
            top: 24%;
            left: 0;
            right: 0;
            z-index: 5;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .storage-widget .storage-percent {
            display: flex;
            align-items: baseline;
            font-size:
                clamp(
                    38px,
                    8vw,
                    48px
                );
            line-height: .95;
            font-weight: 850;
            letter-spacing: -2.8px;
            font-variant-numeric: tabular-nums;
            text-shadow:
                0 0 20px
                rgba(
                    var(--storage-color-rgb),
                    .20
                );
        }

        .storage-widget .storage-percent-symbol {
            margin-left: 3px;
            font-size: .45em;
            font-weight: 700;
            letter-spacing: 0;
        }

        .storage-widget .storage-label {
            margin-top: 7px;
            font-size: 8px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: 2px;
            color: rgb(var(--gray-500));
        }

        .storage-widget .storage-usage {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 7px;
            font-size: 10px;
            line-height: 1;
            font-weight: 500;
            color: rgb(var(--gray-500));
            font-variant-numeric: tabular-nums;
        }

        .storage-widget .storage-usage strong {
            color: rgb(var(--gray-700));
            font-weight: 750;
        }

        .dark .storage-widget .storage-usage strong {
            color: rgb(var(--gray-200));
        }

        .storage-widget .storage-drive {
            position: absolute;
            left: 50%;
            bottom: 1%;
            width: 34%;
            transform:
                translateX(-50%)
                rotate(-3deg);
            z-index: 4;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .storage-widget .storage-ssd-image {
            position: relative;
            width: 100%;
            height: auto;
            object-fit: contain;
            filter:
                drop-shadow(
                    0 9px 14px
                    rgba(0, 0, 0, .32)
                );
            transition:
                transform .35s ease,
                filter .35s ease;
        }

        .storage-widget .storage-gauge:hover
        .storage-ssd-image {
            transform: translateY(-4px);
            filter:
                drop-shadow(
                    0 13px 19px
                    rgba(0, 0, 0, .38)
                );
        }

        .storage-widget .storage-drive-glow {
            position: absolute;
            width: 75%;
            height: 30%;
            bottom: 1%;
            border-radius: 50%;
            background:
                radial-gradient(
                    ellipse,
                    rgba(
                        var(--storage-color-rgb),
                        .30
                    ),
                    transparent 70%
                );
            filter: blur(9px);
        }

        .storage-widget .storage-info {
            width: 100%;
            min-width: 0;
            margin: 0;
            padding: 0;
            align-self: end;
            display: grid;
            grid-template-columns:
                minmax(0, 1fr)
                minmax(0, 1fr);
            border-top:
                1px solid
                rgba(0, 0, 0, .05);
        }

        .storage-widget .storage-info-card {
            position: relative;
            min-width: 0;
            min-height: 58px;
            padding: 8px 8px 0;
            margin-bottom: 0;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .storage-widget .storage-info-card-left {
            justify-content: flex-start;
            text-align: left;
            padding-left: 4px;
        }

        .storage-widget .storage-info-card-right {
            justify-content: flex-end;
            text-align: right;
            border-left:
                1px solid
                rgba(0, 0, 0, .06);
            padding-left: 10px;
        }

        .storage-widget .storage-info-icon {
            width: 32px;
            height: 32px;
            flex: 0 0 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition:
                transform .2s ease,
                background-color .2s ease;
        }

        .storage-widget .storage-info-icon svg {
            width: 17px;
            height: 17px;
        }

        .storage-widget .storage-info-icon-free {
            color: rgb(34, 197, 94);
            background: rgba(34, 197, 94, .10);
            box-shadow:
                inset 0 0 0 1px
                rgba(34, 197, 94, .06);
        }

        .storage-widget .storage-info-icon-disk {
            color: rgb(99, 102, 241);
            background: rgba(99, 102, 241, .10);
            box-shadow:
                inset 0 0 0 1px
                rgba(99, 102, 241, .06);
        }

        .storage-widget .storage-info-card:hover
        .storage-info-icon {
            transform: translateY(-1px);
        }

        .storage-widget .storage-info-content {
            min-width: 0;
        }

        .storage-widget .storage-info-label {
            color: rgb(156, 163, 175);
            font-size: 9px;
            font-weight: 600;
            line-height: 13px;
            letter-spacing: .6px;
            text-transform: uppercase;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .storage-widget .storage-info-value {
            margin-top: 2px;
            color: rgb(17, 24, 39);
            font-size: 14px;
            font-weight: 700;
            line-height: 20px;
            letter-spacing: -.2px;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .storage-widget .storage-info-card-right
        .storage-info-content {
            order: 1;
        }

        .storage-widget .storage-info-card-right
        .storage-info-icon {
            order: 2;
        }

        .dark .storage-widget .storage-info {
            border-color:
                rgba(255, 255, 255, .05);
        }

        .dark .storage-widget .storage-info-card-right {
            border-color:
                rgba(255, 255, 255, .06);
        }

        .dark .storage-widget .storage-info-value {
            color: rgb(255, 255, 255);
        }

        .dark .storage-widget .storage-info-icon-free {
            color: rgb(34, 197, 94);
            background: rgba(34, 197, 94, .10);
        }

        .dark .storage-widget .storage-info-icon-disk {
            color: rgb(129, 140, 248);
            background: rgba(99, 102, 241, .12);
        }

        @media (max-width: 420px) {
            .storage-widget .storage-gauge {
                width: min(100%, 190px);
            }

            .storage-widget .storage-percent {
                font-size: 38px;
            }

            .storage-widget .storage-info-card {
                min-height: 54px;
                gap: 6px;
                padding: 8px 2px 0;
            }

            .storage-widget .storage-info-card-right {
                padding-left: 8px;
            }

            .storage-widget .storage-info-icon {
                width: 30px;
                height: 30px;
                flex-basis: 30px;
                border-radius: 8px;
            }

            .storage-widget .storage-info-icon svg {
                width: 15px;
                height: 15px;
            }

            .storage-widget .storage-info-label {
                font-size: 7px;
                letter-spacing: .4px;
            }

            .storage-widget .storage-info-value {
                font-size: 12px;
                line-height: 17px;
            }
        }

        @media (max-width: 640px) {
        .storage-widget .storage-gauge {
            
    margin-bottom: 20px;
        }
}
    </style>

    <x-filament::section
        class="storage-section"
        heading="Storage"
        description="Monitoraggio archiviazione"
        icon="heroicon-o-circle-stack"
    >

        <div
            class="storage-widget"
            x-data="{
                target: {{ $percent }},
                current: 0,
                duration: 2400,
                startTime: null,
                animationFrame: null,

                startAnimation() {
                    cancelAnimationFrame(this.animationFrame);

                    this.current = 0;
                    this.startTime = null;

                    const animate = (timestamp) => {
                        if (!this.startTime) {
                            this.startTime = timestamp;
                        }

                        const elapsed =
                            timestamp -
                            this.startTime;

                        const progress = Math.min(
                            elapsed / this.duration,
                            1
                        );

                        const eased =
                            progress < 0.5
                                ? 4 *
                                  progress *
                                  progress *
                                  progress
                                : 1 -
                                  Math.pow(
                                      -2 *
                                      progress +
                                      2,
                                      3
                                  ) / 2;

                        this.current =
                            this.target *
                            eased;

                        if (this.$refs.progress) {
                            const circumference = 741.42;
                            const arc =
                                circumference *
                                0.74;

                            this.$refs.progress.style
                                .strokeDashoffset =
                                arc -
                                (
                                    arc *
                                    this.current /
                                    100
                                );
                        }

                        if (progress < 1) {
                            this.animationFrame =
                                requestAnimationFrame(
                                    animate
                                );
                        } else {
                            this.current =
                                this.target;
                        }
                    };

                    this.animationFrame =
                        requestAnimationFrame(
                            animate
                        );
                }
            }"
            x-init="
                $nextTick(() => {
                    startAnimation();
                });
            "
            style="
                --storage-color: {{ $color }};
                --storage-color-rgb: {{ $r }}, {{ $g }}, {{ $b }};
            "
        >

            <div class="storage-main">

                <div class="storage-gauge">

                    <svg
                        class="storage-gauge-svg"
                        viewBox="0 0 300 300"
                        aria-hidden="true"
                    >

                        <circle
                            class="storage-gauge-track"
                            cx="150"
                            cy="150"
                            r="118"
                        />

                        <circle
                            x-ref="progress"
                            class="storage-gauge-progress"
                            cx="150"
                            cy="150"
                            r="118"
                        />

                    </svg>

                    <div class="storage-center">

                        <div
                            class="storage-percent"
                            style="color: var(--storage-color)"
                        >

                            <span x-text="Math.round(current)">
                                0
                            </span>

                            <span class="storage-percent-symbol">
                                %
                            </span>

                        </div>

                        <div class="storage-label">
                            UTILIZZATO
                        </div>

                        <div class="storage-usage">

                            <strong>
                                {{ $used }}
                            </strong>

                            <span>/</span>

                            <span>
                                {{ $total }}
                            </span>

                        </div>

                    </div>

                    <div class="storage-drive">

                        <div class="storage-drive-glow"></div>

                        <img
                            src="{{ asset('images/ssd.png') }}"
                            alt="SSD"
                            class="storage-ssd-image"
                        >

                    </div>

                </div>

            </div>

            <div class="storage-info">

                <div
                    class="
                        storage-info-card
                        storage-info-card-left
                    "
                >

                    <div
                        class="
                            storage-info-icon
                            storage-info-icon-free
                        "
                    >

                        <x-filament::icon
                            icon="heroicon-o-circle-stack"
                        />

                    </div>

                    <div class="storage-info-content">

                        <div class="storage-info-label">
                            Spazio libero
                        </div>

                        <div class="storage-info-value">
                            {{ $free }}
                        </div>

                    </div>

                </div>

                <div
                    class="
                        storage-info-card
                        storage-info-card-right
                    "
                >

                    <div class="storage-info-content">

                        <div class="storage-info-label">
                            Tipo disco
                        </div>

                        <div class="storage-info-value">
                            NVMe SSD
                        </div>

                    </div>

                    <div
                        class="
                            storage-info-icon
                            storage-info-icon-disk
                        "
                    >

                        <x-filament::icon
                            icon="heroicon-o-square-3-stack-3d"
                        />

                    </div>

                </div>

            </div>

        </div>

    </x-filament::section>

</x-filament-widgets::widget>