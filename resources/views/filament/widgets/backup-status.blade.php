@php
    $data = $this->getBackupData();

    $backups = $data['backups'];
    $latestBackup = $data['latestBackup'];
    $backupToday = $data['backupToday'];
    $todayCount = $data['todayCount'];
    $totalSize = $data['totalSize'];

    $latestBackupAgo = $latestBackup
        ? $latestBackup['date']->diffForHumans()
        : null;
@endphp

<x-filament-widgets::widget>

    <style>
        .backup-card {
            --backup-bg: 255 255 255;
            --backup-surface: 248 250 252;
            --backup-border: 229 231 235;

            --backup-text: 17 24 39;
            --backup-secondary: 107 114 128;
            --backup-muted: 156 163 175;

            --backup-success: 34 197 94;
            --backup-warning: 245 158 11;
            --backup-blue: 59 130 246;

            position: relative;

            display: flex;

            width: 100%;
            height: 100%;
            min-height: 100%;

            overflow: hidden;

            box-sizing: border-box;

            border: 1px solid rgb(var(--backup-border));
            border-radius: .95rem;

            background:
                rgb(var(--backup-bg));

            color:
                rgb(var(--backup-text));

            text-align: left;
            font: inherit;

            box-shadow:
                0 1px 2px rgb(0 0 0 / .04),
                0 8px 24px rgb(0 0 0 / .045);

            cursor: pointer;

            transition:
                border-color .18s ease,
                box-shadow .18s ease,
                transform .18s ease;
        }


        .backup-card:hover {
            border-color:
                rgb(209 213 219);

            box-shadow:
                0 2px 4px rgb(0 0 0 / .04),
                0 14px 34px rgb(0 0 0 / .08);

            transform:
                translateY(-1px);
        }


        .backup-card:active {
            transform:
                translateY(0);
        }


        .backup-card:disabled {
            cursor: wait;
            opacity: .65;
        }


        .dark .backup-card {
            --backup-bg: 24 24 27;
            --backup-surface: 30 30 34;
            --backup-border: 39 39 42;

            --backup-text: 244 244 245;
            --backup-secondary: 161 161 170;
            --backup-muted: 113 113 122;

            border-color:
                rgb(var(--backup-border));

            box-shadow:
                0 1px 2px rgb(0 0 0 / .18),
                0 10px 28px rgb(0 0 0 / .18);
        }


        .dark .backup-card:hover {
            border-color:
                rgb(63 63 70);

            box-shadow:
                0 2px 5px rgb(0 0 0 / .25),
                0 16px 36px rgb(0 0 0 / .25);
        }


        .backup-content {
            position: relative;

            display: flex;

            flex: 1 1 auto;

            flex-direction: column;

            width: 100%;
            min-width: 0;
            min-height: 100%;

            padding: 1.15rem;
        }


        .backup-top {
            display: flex;

            align-items: flex-start;
            justify-content: space-between;

            gap: 1rem;

            padding-bottom: 1rem;

            border-bottom:
                1px solid
                rgb(var(--backup-border));
        }


        .backup-identity {
            display: flex;

            align-items: center;

            min-width: 0;

            gap: .75rem;
        }


        .backup-icon {
            display: flex;

            align-items: center;
            justify-content: center;

            flex: 0 0 2.75rem;

            width: 2.75rem;
            height: 2.75rem;

            border-radius: .72rem;

            background:
                rgb(var(--backup-success) / .10);

            color:
                rgb(22 163 74);

            transition:
                transform .18s ease,
                background-color .18s ease;
        }


        .backup-card:hover .backup-icon {
            background:
                rgb(var(--backup-success) / .14);

            transform:
                scale(1.035);
        }


        .backup-warning .backup-icon {
            background:
                rgb(var(--backup-warning) / .11);

            color:
                rgb(217 119 6);
        }


        .dark .backup-icon {
            color:
                rgb(74 222 128);
        }


        .dark .backup-warning .backup-icon {
            color:
                rgb(251 191 36);
        }


        .backup-icon svg {
            width: 1.3rem;
            height: 1.3rem;
        }


        .backup-heading {
            min-width: 0;
        }


        .backup-title {
            overflow: hidden;

            font-size: .92rem;
            font-weight: 750;
            line-height: 1.25;

            white-space: nowrap;
            text-overflow: ellipsis;

            color:
                rgb(var(--backup-text));
        }


        .backup-subtitle {
            margin-top: .25rem;

            font-size: .66rem;
            line-height: 1.3;

            color:
                rgb(var(--backup-secondary));
        }


        .backup-status {
            display: inline-flex;

            align-items: center;

            flex-shrink: 0;

            gap: .4rem;

            padding: .38rem .6rem;

            border-radius: 999px;

            background:
                rgb(var(--backup-success) / .09);

            color:
                rgb(22 163 74);

            font-size: .57rem;
            font-weight: 750;
            line-height: 1;
        }


        .dark .backup-status {
            color:
                rgb(74 222 128);
        }


        .backup-warning .backup-status {
            background:
                rgb(var(--backup-warning) / .10);

            color:
                rgb(217 119 6);
        }


        .dark .backup-warning .backup-status {
            color:
                rgb(251 191 36);
        }


        .backup-status-dot {
            width: .36rem;
            height: .36rem;

            border-radius: 50%;

            background:
                currentColor;

            box-shadow:
                0 0 0 3px
                rgb(var(--backup-success) / .08);
        }


        .backup-warning .backup-status-dot {
            box-shadow:
                0 0 0 3px
                rgb(var(--backup-warning) / .08);
        }


        .backup-overview {
            display: flex;

            align-items: center;

            gap: .8rem;

            margin-top: 1.05rem;

            padding: .8rem .85rem;

            border: 1px solid
                rgb(var(--backup-border));

            border-radius: .72rem;

            background:
                rgb(var(--backup-surface));
        }


        .backup-overview-indicator {
            position: relative;

            display: flex;

            align-items: center;
            justify-content: center;

            flex: 0 0 2rem;

            width: 2rem;
            height: 2rem;
        }


        .backup-overview-indicator::before {
            content: "";

            position: absolute;

            inset: 0;

            border-radius: 50%;

            background:
                rgb(var(--backup-success) / .10);
        }


        .backup-warning .backup-overview-indicator::before {
            background:
                rgb(var(--backup-warning) / .10);
        }


        .backup-overview-indicator svg {
            position: relative;

            z-index: 1;

            width: .95rem;
            height: .95rem;

            color:
                rgb(var(--backup-success));
        }


        .backup-warning .backup-overview-indicator svg {
            color:
                rgb(var(--backup-warning));
        }


        .backup-overview-content {
            min-width: 0;
        }


        .backup-overview-label {
            font-size: .56rem;
            font-weight: 700;

            letter-spacing: .055em;
            text-transform: uppercase;

            color:
                rgb(var(--backup-muted));
        }


        .backup-overview-value {
            margin-top: .18rem;

            overflow: hidden;

            font-size: .7rem;
            font-weight: 650;
            line-height: 1.3;

            white-space: nowrap;
            text-overflow: ellipsis;

            color:
                rgb(var(--backup-text));
        }


        .backup-overview-time {
            margin-left: auto;

            flex-shrink: 0;

            font-size: .58rem;
            font-weight: 550;

            color:
                rgb(var(--backup-muted));
        }


        .backup-description {
            margin-top: .85rem;

            font-size: .68rem;
            line-height: 1.55;

            color:
                rgb(var(--backup-secondary));
        }


        .backup-metrics {
            display: grid;

            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            margin-top: .95rem;

            overflow: hidden;

            border-top:
                1px solid
                rgb(var(--backup-border));

            border-bottom:
                1px solid
                rgb(var(--backup-border));
        }


        .backup-metric {
            min-width: 0;

            padding:
                .72rem .75rem;
        }


        .backup-metric + .backup-metric {
            border-left:
                1px solid
                rgb(var(--backup-border));
        }


        .backup-metric-label {
            display: block;

            margin-bottom: .28rem;

            font-size: .53rem;
            font-weight: 700;

            letter-spacing: .06em;
            text-transform: uppercase;

            color:
                rgb(var(--backup-muted));
        }


        .backup-metric-value {
            overflow: hidden;

            font-size: .74rem;
            font-weight: 750;
            line-height: 1.35;

            white-space: nowrap;
            text-overflow: ellipsis;

            color:
                rgb(var(--backup-text));
        }


        .backup-metric-secondary {
            margin-top: .12rem;

            overflow: hidden;

            font-size: .56rem;
            line-height: 1.3;

            white-space: nowrap;
            text-overflow: ellipsis;

            color:
                rgb(var(--backup-muted));
        }


        .backup-footer {
            display: flex;

            align-items: center;
            justify-content: space-between;

            gap: 1rem;

            margin-top: auto;
            padding-top: 1rem;
        }


        .backup-protection {
            display: inline-flex;

            align-items: center;

            min-width: 0;

            gap: .42rem;

            font-size: .59rem;
            font-weight: 550;

            color:
                rgb(var(--backup-muted));
        }


        .backup-protection svg {
            width: .85rem;
            height: .85rem;

            flex-shrink: 0;

            color:
                rgb(var(--backup-success));
        }


        .backup-warning .backup-protection svg {
            color:
                rgb(var(--backup-warning));
        }


        .backup-action {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            flex-shrink: 0;

            gap: .42rem;

            min-height: 2rem;

            padding:
                .45rem .72rem;

            border:
                1px solid
                rgb(var(--backup-border));

            border-radius: .58rem;

            background:
                rgb(var(--backup-bg));

            color:
                rgb(var(--backup-secondary));

            font-size: .59rem;
            font-weight: 700;

            transition:
                border-color .18s ease,
                background-color .18s ease,
                color .18s ease;
        }


        .backup-card:hover .backup-action {
            border-color:
                rgb(156 163 175);

            background:
                rgb(var(--backup-surface));

            color:
                rgb(var(--backup-text));
        }


        .dark .backup-action {
            background:
                rgb(32 32 36);

            border-color:
                rgb(63 63 70);
        }


        .dark .backup-card:hover .backup-action {
            border-color:
                rgb(82 82 91);

            background:
                rgb(39 39 42);
        }


        .backup-action svg {
            width: .78rem;
            height: .78rem;
        }


        .backup-loading {
            display: inline-flex;

            align-items: center;

            gap: .4rem;
        }


        .backup-spinner {
            width: .78rem;
            height: .78rem;

            border:
                2px solid
                currentColor;

            border-right-color:
                transparent;

            border-radius: 50%;

            animation:
                backup-spin .7s linear infinite;
        }


        @keyframes backup-spin {
            to {
                transform: rotate(360deg);
            }
        }


        @media (max-width: 640px) {

            .backup-content {
                padding: 1rem;
            }

            .backup-top {
                align-items: flex-start;
            }

            .backup-status {
                margin-top: .05rem;
            }

            .backup-overview {
                align-items: flex-start;
            }

            .backup-overview-time {
                display: none;
            }

            .backup-metrics {
                grid-template-columns: 1fr;
            }

            .backup-metric + .backup-metric {
                border-top:
                    1px solid
                    rgb(var(--backup-border));

                border-left: 0;
            }

            .backup-footer {
                align-items: stretch;

                flex-direction: column;
            }

            .backup-action {
                width: 100%;
            }
        }


        @media (prefers-reduced-motion: reduce) {

            .backup-card,
            .backup-icon,
            .backup-action,
            .backup-health-bar {
                transition: none;
            }

            .backup-spinner {
                animation: none;
            }
        }

        .backup-title {
    font-size: 1.15rem;
    font-weight: 750;
}

.backup-subtitle {
    font-size: 0.85rem;
}

.backup-status {
    font-size: 0.78rem;
    font-weight: 700;
}

.backup-overview-label {
    font-size: 0.8rem;
    font-weight: 650;
}

.backup-overview-value {
    font-size: 1rem;
    font-weight: 700;
}

.backup-description {
    font-size: 0.9rem;
    line-height: 1.5;
}

.backup-metric-label {
    font-size: 0.75rem;
    font-weight: 650;
}

.backup-metric-value {
    font-size: 1.05rem;
    font-weight: 750;
}


.backup-footer-text {
    font-size: 0.85rem;
    font-weight: 650;
}

.backup-action {
    font-size: 0.85rem;
    font-weight: 750;
}
    </style>


    <button
        type="button"
        wire:click="runBackup"
        wire:loading.attr="disabled"
        wire:target="runBackup"
        class="
            backup-card
            {{ $backupToday ? '' : 'backup-warning' }}
        "
    >

        <div class="backup-content">


            <div class="backup-top">

                <div class="backup-identity">

                    <div class="backup-icon">

                        <span
                            wire:loading.remove
                            wire:target="runBackup"
                        >
                            <x-heroicon-o-cloud-arrow-up />
                        </span>

                        <span
                            wire:loading
                            wire:target="runBackup"
                        >
                            <svg
                                class="backup-spinner"
                                viewBox="0 0 24 24"
                                fill="none"
                            >
                                <circle
                                    cx="12"
                                    cy="12"
                                    r="9"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    opacity=".25"
                                />

                                <path
                                    d="M21 12a9 9 0 0 0-9-9"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </span>

                    </div>


                    <div class="backup-heading">

                        <div class="backup-title">
                            {{ $backupToday
                                ? 'Backup completato'
                                : 'Backup non eseguito' }}
                        </div>

                        <div class="backup-subtitle">
                            Sistema di backup automatico
                        </div>

                    </div>

                </div>


                <div class="backup-status">

                    <span class="backup-status-dot"></span>

                    {{ $backupToday
                        ? 'Operativo'
                        : 'In attesa' }}

                </div>

            </div>


            <div class="backup-overview">

                <div class="backup-overview-indicator">

                    @if ($backupToday)

                        <x-heroicon-o-check />

                    @else

                        <x-heroicon-o-exclamation-triangle />

                    @endif

                </div>


                <div class="backup-overview-content">

                    <div class="backup-overview-label">
                        Ultima operazione
                    </div>

                    <div class="backup-overview-value">

                        @if ($latestBackup)

                            {{ $latestBackup['date']->translatedFormat('d M Y') }}
                            ·
                            {{ $latestBackup['date']->format('H:i') }}

                        @else

                            Nessun backup disponibile

                        @endif

                    </div>

                </div>


                @if ($latestBackupAgo)

                    <div class="backup-overview-time">
                        {{ $latestBackupAgo }}
                    </div>

                @endif

            </div>


            <div class="backup-description">

                @if ($backupToday)

                    Tutti i dati sono stati salvati correttamente.
                    Il sistema è protetto con il backup giornaliero.

                @elseif ($latestBackup)

                    Il backup giornaliero non è ancora stato eseguito.
                    Esegui un nuovo backup per proteggere i dati più recenti.

                @else

                    Non è presente alcun backup.
                    Esegui il primo backup per iniziare la protezione dei dati.

                @endif

            </div>


            <div class="backup-metrics">


                <div class="backup-metric">

                    <span class="backup-metric-label">
                        Dimensione
                    </span>

                    <div class="backup-metric-value">
                        {{ $latestBackup['size'] ?? '—' }}
                    </div>

                    @if ($totalSize)

                        <div class="backup-metric-secondary">
                            Totale {{ $totalSize }}
                        </div>

                    @endif

                </div>


                <div class="backup-metric">

                    <span class="backup-metric-label">
                        Backup oggi
                    </span>

                    <div class="backup-metric-value">
                        {{ $todayCount }}
                    </div>

                    <div class="backup-metric-secondary">
                        {{ $todayCount === 1
                            ? 'operazione eseguita'
                            : 'operazioni eseguite' }}
                    </div>

                </div>


                <div class="backup-metric">

                    <span class="backup-metric-label">
                        Stato
                    </span>

                    <div class="backup-metric-value">
                        {{ $backupToday
                            ? 'Protetto'
                            : 'Da eseguire' }}
                    </div>

                    <div class="backup-metric-secondary">
                        {{ $backupToday
                            ? 'Backup giornaliero OK'
                            : 'Richiede attenzione' }}
                    </div>

                </div>


            </div>

            <div class="backup-footer">

                <div class="backup-protection">

                    @if ($backupToday)

                        <x-heroicon-o-shield-check />

                        <span>
                            Sistema protetto
                        </span>

                    @else

                        <x-heroicon-o-shield-exclamation />

                        <span>
                            Backup giornaliero richiesto
                        </span>

                    @endif

                </div>


                <div class="backup-action">

                    <span
                        wire:loading.remove
                        wire:target="runBackup"
                    >
                        Esegui backup
                    </span>

                    <span
                        class="backup-loading"
                        wire:loading
                        wire:target="runBackup"
                    >
                        <span class="backup-spinner"></span>

                        Backup in corso...
                    </span>

                    <x-heroicon-o-arrow-down-tray
                        wire:loading.remove
                        wire:target="runBackup"
                    />

                </div>

            </div>

        </div>

    </button>


    @script

    <script>
        $wire.on('backup-download', ({ url }) => {
            const link = document.createElement('a');

            link.href = url;
            link.style.display = 'none';

            document.body.appendChild(link);

            link.click();

            link.remove();
        });
    </script>

    @endscript

</x-filament-widgets::widget>