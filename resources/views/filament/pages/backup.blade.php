<x-filament-panels::page>

    @php

        /*
        |--------------------------------------------------------------------------
        | BACKUPS
        |--------------------------------------------------------------------------
        */

        $backups = collect($this->getBackups())
            ->sortByDesc('date')
            ->values();

        $latestBackup = $backups->first();

        $todayBackups = $backups->filter(
            fn ($backup) => $backup['date']->isToday()
        );

        $backupToday = $todayBackups->isNotEmpty();

        $todayCount = $todayBackups->count();


        /*
        |--------------------------------------------------------------------------
        | TOTAL SIZE
        |--------------------------------------------------------------------------
        */

        $totalSizeBytes = $backups->sum(
            fn ($backup) => $backup['size_bytes'] ?? 0
        );


        /*
        |--------------------------------------------------------------------------
        | HUMAN TOTAL SIZE
        |--------------------------------------------------------------------------
        */

        if ($totalSizeBytes >= 1024 ** 3) {

            $totalSize = number_format(
                $totalSizeBytes / (1024 ** 3),
                2,
                ',',
                '.'
            ) . ' GB';

        } elseif ($totalSizeBytes >= 1024 ** 2) {

            $totalSize = number_format(
                $totalSizeBytes / (1024 ** 2),
                2,
                ',',
                '.'
            ) . ' MB';

        } elseif ($totalSizeBytes >= 1024) {

            $totalSize = number_format(
                $totalSizeBytes / 1024,
                2,
                ',',
                '.'
            ) . ' KB';

        } else {

            $totalSize = $totalSizeBytes . ' B';
        }


        /*
        |--------------------------------------------------------------------------
        | LAST BACKUP
        |--------------------------------------------------------------------------
        */

        $latestBackupAgo = $latestBackup
            ? $latestBackup['date']->diffForHumans()
            : null;

    @endphp


    <style>

        /* =========================================================
         * ROOT
         * ========================================================= */

        .backup-page {

            --backup-card: var(--gray-0);
            --backup-border: var(--gray-200);

            --backup-text: var(--gray-950);
            --backup-muted: var(--gray-500);

            --backup-primary: var(--primary-600);
            --backup-primary-light: var(--primary-50);

            --backup-success: var(--success-600);
            --backup-success-light: var(--success-50);

            --backup-warning: var(--warning-600);
            --backup-warning-light: var(--warning-50);

            --backup-danger: var(--danger-600);
            --backup-danger-light: var(--danger-50);

            color: var(--backup-text);
        }


        /* =========================================================
         * DARK MODE
         * ========================================================= */

        .dark .backup-page {

            --backup-card: var(--gray-900);
            --backup-border: var(--gray-800);

            --backup-text: var(--gray-50);
            --backup-muted: var(--gray-400);

            --backup-primary-light: color-mix(
                in srgb,
                var(--primary-500) 13%,
                transparent
            );

            --backup-success-light: color-mix(
                in srgb,
                var(--success-500) 13%,
                transparent
            );

            --backup-warning-light: color-mix(
                in srgb,
                var(--warning-500) 13%,
                transparent
            );

            --backup-danger-light: color-mix(
                in srgb,
                var(--danger-500) 13%,
                transparent
            );
        }


        /* =========================================================
         * HEADER
         * ========================================================= */

        .backup-page-header {

            display: flex;
            align-items: center;

            gap: 24px;

            margin-bottom: 24px;
        }

        .backup-page-heading {

            display: flex;
            align-items: center;

            gap: 14px;

            min-width: 0;
        }

        .backup-page-icon {

            display: flex;
            align-items: center;
            justify-content: center;

            width: 50px;
            height: 50px;

            flex-shrink: 0;

            border-radius: 14px;

            background: var(--backup-primary-light);
            color: var(--backup-primary);
        }

        .backup-page-icon svg {

            width: 25px;
            height: 25px;
        }

        .backup-page-title {

            margin: 0;

            font-size: 1.35rem;
            line-height: 1.3;
            font-weight: 700;

            color: var(--backup-text);
        }

        .backup-page-description {

            margin-top: 5px;

            font-size: 0.875rem;
            line-height: 1.5;

            color: var(--backup-muted);
        }


        /* =========================================================
         * SUMMARY
         * ========================================================= */

        .backup-summary {

            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 14px;

            margin-bottom: 22px;
        }

        .backup-summary-card {

            display: flex;
            align-items: center;

            gap: 13px;

            min-width: 0;

            padding: 16px;

            background: var(--backup-card);

            border: 1px solid var(--backup-border);

            border-radius: 14px;

            box-shadow:
                0 1px 2px rgb(0 0 0 / 0.03);

            transition:
                transform 150ms ease,
                box-shadow 150ms ease;
        }

        .backup-summary-card:hover {

            transform: translateY(-1px);

            box-shadow:
                0 7px 20px rgb(0 0 0 / 0.05);
        }

        .backup-summary-icon {

            display: flex;
            align-items: center;
            justify-content: center;

            width: 42px;
            height: 42px;

            flex-shrink: 0;

            border-radius: 11px;

            background: var(--backup-primary-light);
            color: var(--backup-primary);
        }

        .backup-summary-icon.success {

            background: var(--backup-success-light);
            color: var(--backup-success);
        }

        .backup-summary-icon.warning {

            background: var(--backup-warning-light);
            color: var(--backup-warning);
        }

        .backup-summary-icon svg {

            width: 20px;
            height: 20px;
        }

        .backup-summary-content {

            min-width: 0;
        }

        .backup-summary-label {

            margin-bottom: 3px;

            font-size: 0.72rem;
            font-weight: 500;

            color: var(--backup-muted);
        }

        .backup-summary-value {

            overflow: hidden;

            font-size: 0.95rem;
            font-weight: 700;

            color: var(--backup-text);

            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .backup-summary-value.success {

            color: var(--backup-success);
        }

        .backup-summary-value.warning {

            color: var(--backup-warning);
        }


        /* =========================================================
         * STATUS
         * ========================================================= */

        .backup-status {

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 20px;

            margin-bottom: 25px;

            padding: 17px 18px;

            border: 1px solid;

            border-radius: 14px;
        }

        .backup-status.success {

            border-color: color-mix(
                in srgb,
                var(--success-500) 25%,
                var(--backup-border)
            );

            background: var(--backup-success-light);
        }

        .backup-status.warning {

            border-color: color-mix(
                in srgb,
                var(--warning-500) 25%,
                var(--backup-border)
            );

            background: var(--backup-warning-light);
        }

        .backup-status-left {

            display: flex;
            align-items: center;

            gap: 13px;

            min-width: 0;
        }

        .backup-status-icon {

            display: flex;
            align-items: center;
            justify-content: center;

            width: 38px;
            height: 38px;

            flex-shrink: 0;

            border-radius: 10px;
        }

        .backup-status.success .backup-status-icon {

            background: var(--backup-success-light);
            color: var(--backup-success);
        }

        .backup-status.warning .backup-status-icon {

            background: var(--backup-warning-light);
            color: var(--backup-warning);
        }

        .backup-status-icon svg {

            width: 20px;
            height: 20px;
        }

        .backup-status-title {

            font-size: 0.9rem;
            font-weight: 700;

            color: var(--backup-text);
        }

        .backup-status-description {

            margin-top: 2px;

            font-size: 0.78rem;

            color: var(--backup-muted);
        }

        .backup-status-date {

            flex-shrink: 0;

            font-size: 0.78rem;
            font-weight: 600;

            color: var(--backup-muted);
        }


        /* =========================================================
         * LIST HEADER
         * ========================================================= */

        .backup-list-header {

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 15px;

            margin-bottom: 14px;
        }

        .backup-list-title {

            margin: 0;

            font-size: 1rem;
            font-weight: 700;

            color: var(--backup-text);
        }

        .backup-list-count {

            display: inline-flex;
            align-items: center;

            padding: 4px 9px;

            border-radius: 999px;

            background: var(--gray-100);
            color: var(--backup-muted);

            font-size: 0.7rem;
            font-weight: 600;
        }

        .dark .backup-list-count {

            background: var(--gray-800);
        }


        /* =========================================================
         * GRID
         * ========================================================= */

        .backup-grid {

            display: grid;

            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            gap: 16px;
        }


        /* =========================================================
         * CARD
         * ========================================================= */

        .backup-card {

            display: flex;
            flex-direction: column;

            min-width: 0;

            padding: 18px;

            background: var(--backup-card);

            border: 1px solid var(--backup-border);

            border-radius: 17px;

            box-shadow:
                0 1px 2px rgb(0 0 0 / 0.025);

            transition:
                transform 160ms ease,
                box-shadow 160ms ease,
                border-color 160ms ease;
        }

        .backup-card:hover {

            transform: translateY(-2px);

            border-color: color-mix(
                in srgb,
                var(--primary-500) 30%,
                var(--backup-border)
            );

            box-shadow:
                0 10px 25px rgb(0 0 0 / 0.06);
        }


        /* =========================================================
         * CARD TOP
         * ========================================================= */

        .backup-card-top {

            display: flex;
            align-items: flex-start;
            justify-content: space-between;

            gap: 12px;

            margin-bottom: 17px;
        }

        .backup-file-icon {

            display: flex;
            align-items: center;
            justify-content: center;

            width: 44px;
            height: 44px;

            flex-shrink: 0;

            border-radius: 12px;

            background: var(--backup-primary-light);
            color: var(--backup-primary);
        }

        .backup-file-icon svg {

            width: 22px;
            height: 22px;
        }

        .backup-badges {

            display: flex;
            flex-wrap: wrap;

            justify-content: flex-end;

            gap: 5px;
        }

        .backup-badge {

            display: inline-flex;
            align-items: center;

            min-height: 23px;

            padding: 3px 8px;

            border-radius: 999px;

            font-size: 0.67rem;
            line-height: 1;
            font-weight: 600;
        }

        .backup-badge.available {

            background: var(--backup-success-light);
            color: var(--backup-success);
        }

        .backup-badge.today {

            background: var(--backup-primary-light);
            color: var(--backup-primary);
        }

        .backup-badge.latest {

            background: var(--gray-100);
            color: var(--backup-muted);
        }

        .dark .backup-badge.latest {

            background: var(--gray-800);
        }


        /* =========================================================
         * FILE
         * ========================================================= */

        .backup-name {

            margin: 0;

            overflow: hidden;

            font-size: 0.9rem;
            font-weight: 650;
            line-height: 1.45;

            color: var(--backup-text);

            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .backup-date {

            display: flex;
            align-items: center;

            gap: 6px;

            margin-top: 5px;

            font-size: 0.76rem;

            color: var(--backup-muted);
        }

        .backup-date svg {

            width: 14px;
            height: 14px;

            flex-shrink: 0;
        }


        /* =========================================================
         * DETAILS
         * ========================================================= */

        .backup-details {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 10px;

            margin-top: 18px;
            padding-top: 15px;

            border-top: 1px solid var(--backup-border);
        }

        .backup-detail-label {

            margin-bottom: 3px;

            font-size: 0.68rem;
            font-weight: 500;

            color: var(--backup-muted);
        }

        .backup-detail-value {

            overflow: hidden;

            font-size: 0.8rem;
            font-weight: 600;

            color: var(--backup-text);

            text-overflow: ellipsis;
            white-space: nowrap;
        }


        /* =========================================================
         * FOOTER
         * ========================================================= */

        .backup-card-footer {

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 10px;

            margin-top: 17px;
        }

        .backup-available {

            display: inline-flex;
            align-items: center;

            gap: 6px;

            font-size: 0.72rem;
            font-weight: 500;

            color: var(--backup-success);
        }

        .backup-available-dot {

            width: 7px;
            height: 7px;

            border-radius: 50%;

            background: var(--backup-success);
        }

        .backup-card-actions {

            display: flex;
            align-items: center;

            gap: 6px;
        }


        /* =========================================================
         * DOWNLOAD
         * ========================================================= */

        .backup-download {

            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 7px;

            min-height: 34px;

            padding: 7px 11px;

            border: 1px solid var(--backup-border);

            border-radius: 9px;

            background: var(--backup-card);

            color: var(--backup-text);

            font-size: 0.75rem;
            font-weight: 600;

            text-decoration: none;

            transition:
                background 140ms ease,
                border-color 140ms ease,
                color 140ms ease;
        }

        .backup-download:hover {

            background: var(--backup-primary-light);

            border-color: color-mix(
                in srgb,
                var(--primary-500) 30%,
                var(--backup-border)
            );

            color: var(--backup-primary);
        }

        .backup-download svg {

            width: 15px;
            height: 15px;
        }


        /* =========================================================
         * DELETE
         * ========================================================= */

        .backup-delete {

            display: inline-flex;
            align-items: center;
            justify-content: center;

            width: 34px;
            height: 34px;

            padding: 0;

            border: 1px solid var(--backup-border);

            border-radius: 9px;

            background: var(--backup-card);

            color: var(--backup-danger);

            cursor: pointer;

            transition:
                background 140ms ease,
                border-color 140ms ease,
                color 140ms ease;
        }

        .backup-delete:hover {

            background: var(--backup-danger-light);

            border-color: color-mix(
                in srgb,
                var(--danger-500) 30%,
                var(--backup-border)
            );

            color: var(--danger-700);
        }

        .backup-delete svg {

            width: 15px;
            height: 15px;
        }


        /* =========================================================
         * EMPTY
         * ========================================================= */

        .backup-empty {

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            padding: 65px 20px;

            background: var(--backup-card);

            border: 1px dashed var(--backup-border);

            border-radius: 17px;

            text-align: center;
        }

        .backup-empty-icon {

            display: flex;
            align-items: center;
            justify-content: center;

            width: 52px;
            height: 52px;

            margin-bottom: 14px;

            border-radius: 14px;

            background: var(--backup-primary-light);
            color: var(--backup-primary);
        }

        .backup-empty-icon svg {

            width: 25px;
            height: 25px;
        }

        .backup-empty-title {

            margin: 0;

            font-size: 0.95rem;
            font-weight: 700;

            color: var(--backup-text);
        }

        .backup-empty-description {

            max-width: 430px;

            margin: 6px 0 0;

            font-size: 0.8rem;
            line-height: 1.5;

            color: var(--backup-muted);
        }


        /* =========================================================
         * RESPONSIVE
         * ========================================================= */

        @media (max-width: 1100px) {

            .backup-summary {

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            .backup-grid {

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }
        }


        @media (max-width: 700px) {

            .backup-page-header {

                align-items: stretch;
                flex-direction: column;
            }

            .backup-summary {

                grid-template-columns: 1fr;
            }

            .backup-grid {

                grid-template-columns: 1fr;
            }

            .backup-status {

                align-items: flex-start;
                flex-direction: column;
            }

            .backup-status-date {

                padding-left: 51px;
            }
        }


        @media (max-width: 480px) {

            .backup-page-heading {

                gap: 11px;
            }

            .backup-page-icon {

                width: 43px;
                height: 43px;
            }

            .backup-page-title {

                font-size: 1.15rem;
            }

            .backup-card {

                padding: 15px;
            }

            .backup-card-footer {

                align-items: flex-start;
                flex-direction: column;
            }

            .backup-card-actions {

                width: 100%;
            }

            .backup-download {

                flex: 1;
            }
        }

    </style>


    <div class="backup-page">


        {{-- =====================================================
             HEADER INTERNO
        ====================================================== --}}

        <div class="backup-page-header">

            <div class="backup-page-heading">

                <div class="backup-page-icon">

                    <x-heroicon-o-server-stack />

                </div>


                <div>

                    <h1 class="backup-page-title">
                        Gestione Backup
                    </h1>

                    <div class="backup-page-description">
                        Controlla lo stato dei backup e scarica gli archivi disponibili.
                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             SUMMARY
        ====================================================== --}}

        <div class="backup-summary">


            {{-- BACKUP DI OGGI --}}

            <div class="backup-summary-card">

                <div class="backup-summary-icon {{ $backupToday ? 'success' : 'warning' }}">

                    @if($backupToday)

                        <x-heroicon-o-check-circle />

                    @else

                        <x-heroicon-o-exclamation-triangle />

                    @endif

                </div>


                <div class="backup-summary-content">

                    <div class="backup-summary-label">
                        Backup di oggi
                    </div>

                    <div class="backup-summary-value {{ $backupToday ? 'success' : 'warning' }}">

                        {{ $backupToday ? 'Eseguito' : 'Non eseguito' }}

                    </div>

                </div>

            </div>


            {{-- ULTIMO BACKUP --}}

            <div class="backup-summary-card">

                <div class="backup-summary-icon">

                    <x-heroicon-o-clock />

                </div>


                <div class="backup-summary-content">

                    <div class="backup-summary-label">
                        Ultimo backup
                    </div>

                    <div
                        class="backup-summary-value"
                        title="{{ $latestBackup ? $latestBackup['date']->format('d/m/Y H:i:s') : 'Nessun backup' }}"
                    >

                        {{ $latestBackupAgo ?? 'Nessun backup' }}

                    </div>

                </div>

            </div>


            {{-- BACKUP DISPONIBILI --}}

            <div class="backup-summary-card">

                <div class="backup-summary-icon">

                    <x-heroicon-o-archive-box />

                </div>


                <div class="backup-summary-content">

                    <div class="backup-summary-label">
                        Backup disponibili
                    </div>

                    <div class="backup-summary-value">

                        {{ $backups->count() }} / 3

                    </div>

                </div>

            </div>


            {{-- SPAZIO --}}

            <div class="backup-summary-card">

                <div class="backup-summary-icon">

                    <x-heroicon-o-circle-stack />

                </div>


                <div class="backup-summary-content">

                    <div class="backup-summary-label">
                        Spazio utilizzato
                    </div>

                    <div class="backup-summary-value">

                        {{ $totalSize }}

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             TODAY STATUS
        ====================================================== --}}

        <div class="backup-status {{ $backupToday ? 'success' : 'warning' }}">

            <div class="backup-status-left">


                <div class="backup-status-icon">

                    @if($backupToday)

                        <x-heroicon-o-check />

                    @else

                        <x-heroicon-o-exclamation-triangle />

                    @endif

                </div>


                <div>

                    <div class="backup-status-title">

                        @if($backupToday)

                            Backup giornaliero completato

                        @else

                            Backup giornaliero non ancora eseguito

                        @endif

                    </div>


                    <div class="backup-status-description">

                        @if($backupToday)

                            Sono presenti
                            {{ $todayCount }}
                            {{ $todayCount === 1 ? 'backup' : 'backup' }}
                            creati oggi.

                        @else

                            Non è stato trovato alcun backup creato nella giornata di oggi.

                        @endif

                    </div>

                </div>

            </div>


            @if($latestBackup)

                <div class="backup-status-date">

                    Ultimo:
                    {{ $latestBackup['date']->format('d/m/Y H:i') }}

                </div>

            @endif

        </div>


        {{-- =====================================================
             LIST HEADER
        ====================================================== --}}

        <div class="backup-list-header">

            <h2 class="backup-list-title">
                Backup disponibili
            </h2>


            <span class="backup-list-count">

                {{ $backups->count() }} / 3

            </span>

        </div>


        {{-- =====================================================
             EMPTY
        ====================================================== --}}

        @if($backups->isEmpty())

            <div class="backup-empty">

                <div class="backup-empty-icon">

                    <x-heroicon-o-archive-box-x-mark />

                </div>


                <h3 class="backup-empty-title">
                    Nessun backup disponibile
                </h3>


                <p class="backup-empty-description">

                    Non sono ancora presenti backup sul server.
                    Utilizza il pulsante "Esegui Backup Ora"
                    per creare il primo backup.

                </p>

            </div>


        @else


            {{-- =================================================
                 BACKUPS
            ================================================== --}}

            <div class="backup-grid">

                @foreach($backups as $index => $backup)

                    @php

                        $isToday = $backup['date']->isToday();

                        $isLatest = $index === 0;

                    @endphp


                    <div class="backup-card">


                        {{-- =====================================
                             TOP
                        ====================================== --}}

                        <div class="backup-card-top">


                            <div class="backup-file-icon">

                                <x-heroicon-o-archive-box />

                            </div>


                            <div class="backup-badges">


                                @if($isToday)

                                    <span class="backup-badge today">
                                        Oggi
                                    </span>

                                @endif


                                @if($isLatest)

                                    <span class="backup-badge latest">
                                        Ultimo
                                    </span>

                                @endif


                                <span class="backup-badge available">
                                    Disponibile
                                </span>

                            </div>

                        </div>


                        {{-- =====================================
                             FILE
                        ====================================== --}}

                        <div>

                            <h3
                                class="backup-name"
                                title="{{ $backup['name'] }}"
                            >

                                {{ $backup['name'] }}

                            </h3>


                            <div class="backup-date">

                                <x-heroicon-o-calendar-days />

                                <span>
                                    {{ $backup['date']->translatedFormat('d F Y') }}
                                </span>

                                <span>
                                    ·
                                </span>

                                <span>
                                    {{ $backup['date']->format('H:i') }}
                                </span>

                            </div>

                        </div>


                        {{-- =====================================
                             DETAILS
                        ====================================== --}}

                        <div class="backup-details">


                            <div>

                                <div class="backup-detail-label">
                                    Data
                                </div>

                                <div class="backup-detail-value">
                                    {{ $backup['date']->format('d/m/Y') }}
                                </div>

                            </div>


                            <div>

                                <div class="backup-detail-label">
                                    Dimensione
                                </div>

                                <div class="backup-detail-value">
                                    {{ $backup['size'] }}
                                </div>

                            </div>

                        </div>


                        {{-- =====================================
                             FOOTER
                        ====================================== --}}

                        <div class="backup-card-footer">


                            <div class="backup-available">

                                <span class="backup-available-dot"></span>

                                Disponibile

                            </div>


                            <div class="backup-card-actions">


                                {{-- DOWNLOAD --}}

                                <a
                                    href="{{ route('backup.download', ['path' => $backup['path']]) }}"
                                    class="backup-download"
                                    title="Scarica backup"
                                >

                                    <x-heroicon-o-arrow-down-tray />

                                    Scarica

                                </a>


                                {{-- DELETE --}}

                                <button
                                    type="button"
                                    class="backup-delete"
                                    wire:click="deleteBackup(@js($backup['path']))"
                                    wire:confirm="Sei sicuro di voler eliminare questo backup?"
                                    title="Elimina backup"
                                >

                                    <x-heroicon-o-trash />

                                </button>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </div>

</x-filament-panels::page>