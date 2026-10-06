<x-filament-panels::page>

    <div class="services-dashboard">

        @php
            $total = $this->getTotal();
            $active = $this->getActive();
            $delivered = $this->getDelivered();
            $lost = $this->getLost();

            $returned = $this->getReturned();
            $completed = $this->getCompleted();

            $toVerify = $active + $lost;
        @endphp


        {{-- =====================================================
             HEADER
        ====================================================== --}}

        <div class="services-header">

            <div>

                <div class="services-title">
                    Gestione servizi
                </div>

                <div class="services-subtitle">
                    Panoramica dello stato dei servizi e delle attività.
                </div>

            </div>


            <div class="services-badge">

                <span class="services-badge-dot"></span>

                Monitoraggio servizi

            </div>

        </div>


        {{-- =====================================================
             KPI PRINCIPALI
        ====================================================== --}}

        <div class="services-kpi-grid">


            {{-- TOTALE --}}

            <div class="services-kpi services-kpi-primary">

                <div class="services-kpi-top">

                    <div>

                        <div class="services-kpi-label">
                            Totale
                        </div>

                        <div class="services-kpi-value">
                            {{ number_format($total, 0, ',', '.') }}
                        </div>

                    </div>


                    <div class="services-kpi-icon">

                        <x-heroicon-o-cube class="h-5 w-5" />

                    </div>

                </div>


                <div class="services-kpi-description">
                    Totale dei servizi presenti nel sistema
                </div>

            </div>


            {{-- ATTIVE --}}

            <div class="services-kpi services-kpi-success">

                <div class="services-kpi-top">

                    <div>

                        <div class="services-kpi-label">
                            Attive
                        </div>

                        <div class="services-kpi-value">
                            {{ number_format($active, 0, ',', '.') }}
                        </div>

                    </div>


                    <div class="services-kpi-icon">

                        <x-heroicon-o-check-circle class="h-5 w-5" />

                    </div>

                </div>


                <div class="services-kpi-description">
                    Servizi attualmente attivi
                </div>

            </div>


            {{-- CONSEGNATE --}}

            <div class="services-kpi services-kpi-primary">

                <div class="services-kpi-top">

                    <div>

                        <div class="services-kpi-label">
                            Consegnate
                        </div>

                        <div class="services-kpi-value">
                            {{ number_format($delivered, 0, ',', '.') }}
                        </div>

                    </div>


                    <div class="services-kpi-icon">

                        <x-heroicon-o-archive-box-arrow-down class="h-5 w-5" />

                    </div>

                </div>


                <div class="services-kpi-description">
                    Servizi già consegnati
                </div>

            </div>


            {{-- SMARRITI --}}

            <div class="services-kpi services-kpi-danger">

                <div class="services-kpi-top">

                    <div>

                        <div class="services-kpi-label">
                            Smarriti
                        </div>

                        <div class="services-kpi-value">
                            {{ number_format($lost, 0, ',', '.') }}
                        </div>

                    </div>


                    <div class="services-kpi-icon">

                        <x-heroicon-o-exclamation-triangle class="h-5 w-5" />

                    </div>

                </div>


                <div class="services-kpi-description">
                    Servizi segnalati come smarriti
                </div>

            </div>


        </div>


        {{-- =====================================================
             STATO DEI SERVIZI
        ====================================================== --}}

        <div class="services-section">


            {{-- HEADER SEZIONE --}}

            <div class="services-section-header">

                <div class="services-section-title-wrap">

                    <div class="services-section-icon services-icon-primary">

                        <x-heroicon-o-chart-bar-square class="h-5 w-5" />

                    </div>


                    <div>

                        <div class="services-section-title">
                            Stato dei servizi
                        </div>

                        <div class="services-section-subtitle">
                            Riepilogo delle principali attività
                        </div>

                    </div>

                </div>

            </div>


            {{-- CONTENUTO --}}

            <div class="services-section-body">


                <div class="services-status-grid">


                    {{-- =================================================
                         RESTITUITE
                    ================================================== --}}

                    <div class="services-status-card services-status-primary">


                        <div class="services-status-icon">

                            <x-heroicon-o-arrow-uturn-left class="h-5 w-5" />

                        </div>


                        <div class="services-status-label">
                            Restituite
                        </div>


                        <div class="services-status-value">
                            {{ number_format($returned, 0, ',', '.') }}
                        </div>


                        <div class="services-status-meta">
                            Servizi restituiti
                        </div>


                    </div>


                    {{-- =================================================
                         CONCLUSE
                    ================================================== --}}

                    <div class="services-status-card services-status-success">


                        <div class="services-status-icon">

                            <x-heroicon-o-check-badge class="h-5 w-5" />

                        </div>


                        <div class="services-status-label">
                            Concluse
                        </div>


                        <div class="services-status-value">
                            {{ number_format($completed, 0, ',', '.') }}
                        </div>


                        <div class="services-status-meta">
                            Servizi completati
                        </div>


                    </div>


                    {{-- =================================================
                         DA VERIFICARE
                    ================================================== --}}

                    <div class="services-status-card services-status-warning">


                        <div class="services-status-icon">

                            <x-heroicon-o-exclamation-circle class="h-5 w-5" />

                        </div>


                        <div class="services-status-label">
                            Da verificare
                        </div>


                        <div class="services-status-value">
                            {{ number_format($toVerify, 0, ',', '.') }}
                        </div>


                        <div class="services-status-meta">
                            Servizi attivi o da controllare
                        </div>


                    </div>


                </div>

            </div>

        </div>


        {{-- =====================================================
             CSS
        ====================================================== --}}

        <style>

            /* =====================================================
               SERVICES DASHBOARD
            ====================================================== */

            .services-dashboard {
                color: rgb(17 24 39);
            }


            /* =====================================================
               HEADER
            ====================================================== */

            .services-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 2rem;
                padding: .5rem .25rem 2rem;
            }


            .services-title {
                font-size: 1.65rem;
                line-height: 1.2;
                font-weight: 750;
                letter-spacing: -.04em;
                color: rgb(17 24 39);
            }


            .services-subtitle {
                margin-top: .55rem;
                font-size: .875rem;
                line-height: 1.6;
                color: rgb(107 114 128);
            }


            .services-badge {
                display: inline-flex;
                align-items: center;
                gap: .55rem;
                padding: .55rem .85rem;
                border: 1px solid rgb(37 99 235 / .18);
                border-radius: 999px;
                background: rgb(239 246 255);
                color: rgb(37 99 235);
                font-size: .73rem;
                font-weight: 650;
                white-space: nowrap;
            }


            .services-badge-dot {
                width: 7px;
                height: 7px;
                border-radius: 50%;
                background: rgb(37 99 235);
                box-shadow:
                    0 0 0 4px rgb(37 99 235 / .10);
            }


            /* =====================================================
               KPI GRID
            ====================================================== */

            .services-kpi-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 1.5rem;
                margin-bottom: 2.5rem;
            }


            /* =====================================================
               KPI CARD
            ====================================================== */

            .services-kpi {

                --kpi-color: rgb(37 99 235);

                position: relative;
                overflow: hidden;

                min-height: 180px;

                padding: 1.5rem;

                border: 1px solid rgb(229 231 235);
                border-radius: 14px;

                background: rgb(255 255 255);

                box-shadow:
                    0 1px 2px rgb(0 0 0 / .04),
                    0 6px 18px rgb(0 0 0 / .04);

                transition:
                    transform .2s ease,
                    border-color .2s ease,
                    box-shadow .2s ease;
            }


            .services-kpi:hover {
                transform: translateY(-2px);

                border-color:
                    color-mix(
                        in srgb,
                        var(--kpi-color) 30%,
                        rgb(229 231 235)
                    );

                box-shadow:
                    0 8px 24px rgb(0 0 0 / .08);
            }


            .services-kpi::before {

                content: "";

                position: absolute;

                top: 0;
                left: 0;
                right: 0;

                height: 3px;

                background: var(--kpi-color);
            }


            .services-kpi-primary {
                --kpi-color: rgb(37 99 235);
            }


            .services-kpi-success {
                --kpi-color: rgb(22 163 74);
            }


            .services-kpi-danger {
                --kpi-color: rgb(220 38 38);
            }


            /* =====================================================
               KPI CONTENT
            ====================================================== */

            .services-kpi-top {

                position: relative;
                z-index: 2;

                display: flex;

                align-items: flex-start;
                justify-content: space-between;

                gap: 1.5rem;
            }


            .services-kpi-label {

                margin-top: 1.15rem;

                font-size: .78rem;

                font-weight: 600;

                color: rgb(107 114 128);
            }


            .services-kpi-value {

                display: block !important;

                margin-top: .45rem;

                font-size: 23px !important;

                line-height: 1.2 !important;

                font-weight: 700 !important;

                letter-spacing: -.03em;

                color: rgb(17 24 39) !important;

                opacity: 1 !important;

                visibility: visible !important;
            }


            .services-kpi-description {

                position: relative;

                z-index: 2;

                margin-top: 1.2rem;

                max-width: 240px;

                font-size: .73rem;

                line-height: 1.5;

                color: rgb(107 114 128);
            }


            /* =====================================================
               KPI ICON
            ====================================================== */

            .services-kpi-icon {

                display: flex;

                align-items: center;
                justify-content: center;

                width: 48px;
                height: 48px;

                flex-shrink: 0;

                border-radius: 13px;

                background: rgb(239 246 255);

                color: var(--kpi-color);
            }


            .services-kpi-success .services-kpi-icon {
                background: rgb(240 253 244);
            }


            .services-kpi-danger .services-kpi-icon {
                background: rgb(254 242 242);
            }


            /* =====================================================
               MAIN SECTION
            ====================================================== */

            .services-section {

                overflow: hidden;

                margin-top: 2rem;

                border: 1px solid rgb(229 231 235);

                border-radius: 14px;

                background: rgb(255 255 255);

                box-shadow:
                    0 1px 2px rgb(0 0 0 / .04),
                    0 6px 18px rgb(0 0 0 / .04);
            }


            /* =====================================================
               SECTION HEADER
            ====================================================== */

            .services-section-header {

                display: flex;

                align-items: center;

                justify-content: space-between;

                gap: 1rem;

                padding: 1.4rem 1.5rem;

                border-bottom:
                    1px solid rgb(229 231 235);
            }


            .services-section-title-wrap {

                display: flex;

                align-items: center;

                gap: 1rem;
            }


            .services-section-icon {

                display: flex;

                align-items: center;
                justify-content: center;

                width: 42px;
                height: 42px;

                flex-shrink: 0;

                border-radius: 12px;
            }


            .services-icon-primary {

                background: rgb(239 246 255);

                color: rgb(37 99 235);
            }


            .services-section-title {

                font-size: .95rem;

                font-weight: 700;

                letter-spacing: -.015em;

                color: rgb(17 24 39);
            }


            .services-section-subtitle {

                margin-top: .25rem;

                font-size: .72rem;

                color: rgb(107 114 128);
            }


            /* =====================================================
               SECTION BODY
            ====================================================== */

            .services-section-body {
                padding: 1.5rem;
            }


            /* =====================================================
               STATUS GRID
            ====================================================== */

            .services-status-grid {

                display: grid;

                grid-template-columns:
                    repeat(3, minmax(0, 1fr));

                gap: 1.5rem;
            }


            /* =====================================================
               STATUS CARD
            ====================================================== */

            .services-status-card {

                position: relative;

                overflow: hidden;

                min-height: 165px;

                padding: 1.5rem;

                border: 1px solid rgb(229 231 235);

                border-radius: 14px;

                background: rgb(255 255 255);

                transition:
                    transform .2s ease,
                    border-color .2s ease,
                    box-shadow .2s ease;
            }


            .services-status-card:hover {

                transform: translateY(-2px);

                border-color:
                    rgb(209 213 219);

                box-shadow:
                    0 8px 24px rgb(0 0 0 / .06);
            }


            /* =====================================================
               STATUS ICON
            ====================================================== */

            .services-status-icon {

                display: flex;

                align-items: center;
                justify-content: center;

                width: 44px;
                height: 44px;

                margin-bottom: 1.25rem;

                border-radius: 12px;
            }


            .services-status-primary .services-status-icon {

                background: rgb(239 246 255);

                color: rgb(37 99 235);
            }


            .services-status-success .services-status-icon {

                background: rgb(240 253 244);

                color: rgb(22 163 74);
            }


            .services-status-warning .services-status-icon {

                background: rgb(255 251 235);

                color: rgb(217 119 6);
            }


            /* =====================================================
               STATUS TEXT
            ====================================================== */

            .services-status-label {

                font-size: .75rem;

                font-weight: 600;

                color: rgb(107 114 128);
            }


            .services-status-value {

                display: block !important;

                margin-top: .5rem;

                font-size: 23px !important;

                line-height: 1.2 !important;

                font-weight: 700 !important;

                letter-spacing: -.03em;

                color: rgb(17 24 39) !important;

                opacity: 1 !important;

                visibility: visible !important;
            }


            .services-status-meta {

                margin-top: .7rem;

                font-size: .7rem;

                line-height: 1.5;

                color: rgb(107 114 128);
            }


            /* =====================================================
               DARK MODE
            ====================================================== */

            .dark .services-dashboard {
                color: rgb(244 244 245);
            }


            .dark .services-title {
                color: rgb(244 244 245);
            }


            .dark .services-kpi,
            .dark .services-section,
            .dark .services-status-card {

                border-color: rgb(39 39 42);

                background: rgb(24 24 27);
            }


            .dark .services-section-header {

                border-color: rgb(39 39 42);
            }


            .dark .services-kpi-value,
            .dark .services-status-value,
            .dark .services-section-title {

                color: rgb(244 244 245) !important;
            }


            .dark .services-kpi-label,
            .dark .services-kpi-description,
            .dark .services-subtitle,
            .dark .services-section-subtitle,
            .dark .services-status-label,
            .dark .services-status-meta {

                color: rgb(161 161 170);
            }


            .dark .services-kpi-icon {

                background:
                    rgb(30 58 138 / .35);
            }


            .dark .services-kpi-success .services-kpi-icon {

                background:
                    rgb(20 83 45 / .35);
            }


            .dark .services-kpi-danger .services-kpi-icon {

                background:
                    rgb(127 29 29 / .35);
            }


            .dark .services-icon-primary,
            .dark .services-status-primary .services-status-icon {

                background:
                    rgb(30 58 138 / .35);
            }


            .dark .services-status-success .services-status-icon {

                background:
                    rgb(20 83 45 / .35);
            }


            .dark .services-status-warning .services-status-icon {

                background:
                    rgb(120 53 15 / .35);
            }


            .dark .services-badge {

                background:
                    rgb(30 58 138 / .35);

                border-color:
                    rgb(37 99 235 / .25);

                color:
                    rgb(96 165 250);
            }


            /* =====================================================
               RESPONSIVE
            ====================================================== */

            @media (max-width: 1200px) {

                .services-kpi-grid {

                    grid-template-columns:
                        repeat(2, minmax(0, 1fr));
                }
            }


            @media (max-width: 900px) {

                .services-status-grid {

                    grid-template-columns: 1fr;
                }


                .services-header {

                    align-items: flex-start;

                    flex-direction: column;
                }
            }


            @media (max-width: 640px) {

                .services-kpi-grid {

                    grid-template-columns: 1fr;

                    gap: 1rem;
                }


                .services-kpi {

                    min-height: 155px;

                    padding: 1.35rem;
                }


                .services-kpi-value {

                    font-size: 1.9rem !important;
                }


                .services-section {

                    margin-top: 1.5rem;

                    border-radius: 14px;
                }


                .services-section-header {

                    padding: 1.15rem;
                }


                .services-section-body {

                    padding: 1.15rem;
                }


                .services-status-card {

                    min-height: 150px;

                    padding: 1.25rem;
                }
            }


            /* =====================================================
               ACCESSIBILITY
            ====================================================== */

            @media (prefers-reduced-motion: reduce) {

                .services-kpi,
                .services-status-card {

                    transition: none;
                }


                .services-kpi:hover,
                .services-status-card:hover {

                    transform: none;
                }
            }

        </style>

    </div>

</x-filament-panels::page>