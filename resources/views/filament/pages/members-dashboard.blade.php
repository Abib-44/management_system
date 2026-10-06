<x-filament-panels::page>

    @php
        /*
        |--------------------------------------------------------------------------
        | DATI DASHBOARD
        |--------------------------------------------------------------------------
        */

        $totalMembers = $this->getTotalMembers();
        $newMembersThisMonth = $this->getNewMembersThisMonth();
        $renewalsNext30Days = $this->getRenewalsNext30Days();
        $expiredRenewals = $this->getExpiredRenewals();

        $totalAnnualFees = $this->getTotalAnnualFees();
        $totalFeesPaidThisYear = $this->getTotalFeesPaidThisYear();
        $membersWithFeePaymentsThisYear = $this->getMembersWithFeePaymentsThisYear();

        $membersByStatus = $this->getMembersByStatus();

        /*
        |--------------------------------------------------------------------------
        | DATI GRAFICI
        |--------------------------------------------------------------------------
        */

        $statusLabels = collect($membersByStatus)
            ->keys()
            ->map(fn ($status) => $status ?: 'Non specificato')
            ->values()
            ->all();

        $statusValues = collect($membersByStatus)
            ->values()
            ->map(fn ($value) => (int) $value)
            ->all();

        $feesLabels = [
            'Quote registrate',
            'Incassato',
        ];

        $feesValues = [
            (float) $totalAnnualFees,
            (float) $totalFeesPaidThisYear,
        ];

        $renewalsLabels = [
            'Prossimi 30 giorni',
            'Scaduti',
        ];

        $renewalsValues = [
            (int) $renewalsNext30Days,
            (int) $expiredRenewals,
        ];
    @endphp

    <style>
        /* =========================================================
           MEMBERS DASHBOARD
           Finance-style / Filament
        ========================================================== */

        .members-dashboard {
            color: rgb(17 24 39);
        }

        /* =========================================================
           HEADER
        ========================================================== */

        .members-dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
            padding: .5rem .25rem 2rem;
        }

        .members-dashboard-title {
            font-size: 1.65rem;
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: -.04em;
            color: rgb(17 24 39);
        }

        .members-dashboard-subtitle {
            margin-top: .55rem;
            font-size: .875rem;
            line-height: 1.6;
            color: rgb(107 114 128);
        }

        .members-dashboard-badge {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .55rem .85rem;
            border: 1px solid rgb(22 163 74 / .15);
            border-radius: 999px;
            background: rgb(240 253 244);
            color: rgb(22 163 74);
            font-size: .73rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .members-dashboard-badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: rgb(22 163 74);
            box-shadow: 0 0 0 4px rgb(22 163 74 / .10);
        }

        /* =========================================================
           KPI GRID
        ========================================================== */

        .members-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        /* =========================================================
           KPI CARD
        ========================================================== */

        .members-kpi {
            position: relative;
            overflow: hidden;
            min-height: 155px;
            padding: 1.5rem;
            border: 1px solid rgb(229 231 235);
            border-radius: 12px;
            background: rgb(255 255 255);
            box-shadow:
                0 1px 2px rgb(0 0 0 / .05),
                0 4px 12px rgb(0 0 0 / .04);
        }

        .members-kpi-primary {
            border-top: 3px solid rgb(37 99 235);
        }

        .members-kpi-success {
            border-top: 3px solid rgb(22 163 74);
        }

        .members-kpi-warning {
            border-top: 3px solid rgb(217 119 6);
        }

        .members-kpi-danger {
            border-top: 3px solid rgb(220 38 38);
        }

        /* =========================================================
           KPI CONTENT
        ========================================================== */

        .members-kpi-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1.5rem;
        }

        .members-kpi-label {
            margin-top: .2rem;
            font-size: .78rem;
            font-weight: 600;
            color: rgb(107 114 128);
        }

        /*
         * IMPORTANTE:
         * forza la visibilità dei numeri contro eventuali override
         * di Filament/Tailwind.
         */

        .members-kpi-value {
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

        .members-kpi-description {
            margin-top: 1.15rem;
            max-width: 250px;
            font-size: .73rem;
            line-height: 1.5;
            color: rgb(107 114 128);
        }

        .members-kpi-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            flex-shrink: 0;
            border-radius: 10px;
        }

        .members-kpi-primary .members-kpi-icon {
            background: rgb(239 246 255);
            color: rgb(37 99 235);
        }

        .members-kpi-success .members-kpi-icon {
            background: rgb(240 253 244);
            color: rgb(22 163 74);
        }

        .members-kpi-warning .members-kpi-icon {
            background: rgb(255 251 235);
            color: rgb(217 119 6);
        }

        .members-kpi-danger .members-kpi-icon {
            background: rgb(254 242 242);
            color: rgb(220 38 38);
        }

        /* =========================================================
           SECTIONS
        ========================================================== */

        .members-section {
            overflow: hidden;
            margin-top: 2.5rem;
            border: 1px solid rgb(229 231 235);
            border-radius: 12px;
            background: rgb(255 255 255);
            box-shadow:
                0 1px 2px rgb(0 0 0 / .05),
                0 4px 12px rgb(0 0 0 / .04);
        }

        /* =========================================================
           SECTION HEADER
        ========================================================== */

        .members-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.35rem 1.5rem;
            border-bottom: 1px solid rgb(229 231 235);
            background: rgb(255 255 255);
        }

        .members-section-title-wrap {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .members-section-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            border-radius: 10px;
            background: rgb(239 246 255);
            color: rgb(37 99 235);
        }

        .members-section-title {
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: -.015em;
            color: rgb(17 24 39);
        }

        .members-section-subtitle {
            margin-top: .25rem;
            font-size: .72rem;
            color: rgb(107 114 128);
        }

        /* =========================================================
           SECTION BODY
        ========================================================== */

        .members-section-body {
            padding: 1.5rem;
        }

        /* =========================================================
           FEES
        ========================================================== */

        .fees-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1.25rem;
        }

        .fee-card {
            position: relative;
            overflow: hidden;
            min-height: 145px;
            padding: 1.4rem;
            border: 1px solid rgb(229 231 235);
            border-radius: 10px;
            background: rgb(255 255 255);
        }

        .fee-card-label {
            font-size: .75rem;
            font-weight: 600;
            color: rgb(107 114 128);
        }

        .fee-card-value {
            display: block !important;
            margin-top: .65rem;
            font-size: 23px !important;
            line-height: 1.2 !important;
            font-weight: 700 !important;
            letter-spacing: -.03em;
            color: rgb(17 24 39) !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .fee-card-income .fee-card-value {
            color: rgb(22 163 74) !important;
        }

        .fee-card-meta {
            margin-top: .75rem;
            font-size: .71rem;
            line-height: 1.5;
            color: rgb(107 114 128);
        }

        /* =========================================================
           CHART GRID
        ========================================================== */

        .members-charts-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.5rem;
        }

        .members-chart-card {
            min-height: 330px;
            padding: 1.25rem;
            border: 1px solid rgb(229 231 235);
            border-radius: 10px;
            background: rgb(255 255 255);
        }

        .members-chart-card-full {
            grid-column: 1 / -1;
        }

        .members-chart-title {
            font-size: .82rem;
            font-weight: 700;
            color: rgb(17 24 39);
        }

        .members-chart-subtitle {
            margin-top: .25rem;
            font-size: .7rem;
            color: rgb(107 114 128);
        }

        .members-chart-wrapper {
            position: relative;
            height: 245px;
            margin-top: 1rem;
        }

        .members-chart-wrapper-small {
            position: relative;
            height: 220px;
            margin-top: 1rem;
        }

        /* =========================================================
           STATUS
        ========================================================== */

        .status-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .status-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            min-height: 78px;
            padding: 1rem 1.15rem;
            border: 1px solid rgb(229 231 235);
            border-radius: 10px;
            background: rgb(255 255 255);
        }

        .status-left {
            display: flex;
            align-items: center;
            gap: .8rem;
            min-width: 0;
        }

        .status-indicator {
            width: 9px;
            height: 9px;
            flex-shrink: 0;
            border-radius: 50%;
            background: rgb(37 99 235);
            box-shadow: 0 0 0 4px rgb(37 99 235 / .10);
        }

        .status-name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: .78rem;
            font-weight: 600;
            color: rgb(17 24 39);
        }

        .status-total {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            height: 34px;
            padding: 0 .65rem;
            border-radius: 8px;
            background: rgb(239 246 255);
            color: rgb(37 99 235);
            font-size: .82rem;
            font-weight: 700;
        }

        /* =========================================================
           SUMMARY
        ========================================================== */

        .members-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .members-summary-item {
            padding: 1rem 1.15rem;
            border: 1px solid rgb(229 231 235);
            border-radius: 10px;
            background: rgb(255 255 255);
        }

        .members-summary-label {
            font-size: .7rem;
            font-weight: 600;
            color: rgb(107 114 128);
        }

        .members-summary-value {
            display: block !important;
            margin-top: .45rem;
            font-size: 20px !important;
            line-height: 1.2 !important;
            font-weight: 700 !important;
            color: rgb(17 24 39) !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* =========================================================
           EMPTY STATE
        ========================================================== */

        .members-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 180px;
            padding: 2.5rem;
            text-align: center;
            border: 1px dashed rgb(229 231 235);
            border-radius: 10px;
            background: rgb(249 250 251);
            color: rgb(107 114 128);
        }

        /* =========================================================
           DARK MODE
        ========================================================== */

        .dark .members-dashboard {
            color: rgb(244 244 245);
        }

        .dark .members-dashboard-title {
            color: rgb(244 244 245);
        }

        .dark .members-dashboard-subtitle {
            color: rgb(161 161 170);
        }

        .dark .members-dashboard-badge {
            border-color: rgb(22 163 74 / .20);
            background: rgb(20 83 45 / .35);
            color: rgb(74 222 128);
        }

        .dark .members-dashboard-badge-dot {
            background: rgb(74 222 128);
            box-shadow: 0 0 0 4px rgb(74 222 128 / .10);
        }

        .dark .members-kpi,
        .dark .members-section,
        .dark .members-chart-card,
        .dark .fee-card,
        .dark .status-card,
        .dark .members-summary-item {
            border-color: rgb(63 63 70);
            background: rgb(24 24 27);
            box-shadow: none;
        }

        .dark .members-section-header {
            border-color: rgb(63 63 70);
            background: rgb(24 24 27);
        }

        .dark .members-kpi-label,
        .dark .members-kpi-description,
        .dark .members-section-subtitle,
        .dark .fee-card-label,
        .dark .fee-card-meta,
        .dark .members-chart-subtitle,
        .dark .members-summary-label {
            color: rgb(161 161 170);
        }

        .dark .members-kpi-value {
            color: rgb(244 244 245) !important;
        }

        .dark .fee-card-value {
            color: rgb(244 244 245) !important;
        }

        .dark .fee-card-income .fee-card-value {
            color: rgb(74 222 128) !important;
        }

        .dark .members-summary-value {
            color: rgb(244 244 245) !important;
        }

        .dark .members-section-title,
        .dark .members-chart-title,
        .dark .status-name {
            color: rgb(244 244 245);
        }

        .dark .members-kpi-primary .members-kpi-icon {
            background: rgb(30 58 138 / .35);
            color: rgb(96 165 250);
        }

        .dark .members-kpi-success .members-kpi-icon {
            background: rgb(20 83 45 / .35);
            color: rgb(74 222 128);
        }

        .dark .members-kpi-warning .members-kpi-icon {
            background: rgb(120 53 15 / .35);
            color: rgb(251 191 36);
        }

        .dark .members-kpi-danger .members-kpi-icon {
            background: rgb(127 29 29 / .35);
            color: rgb(248 113 113);
        }

        .dark .members-section-icon {
            background: rgb(30 58 138 / .35);
            color: rgb(96 165 250);
        }

        .dark .members-chart-card {
            background: rgb(24 24 27);
        }

        .dark .status-total {
            background: rgb(30 58 138 / .35);
            color: rgb(96 165 250);
        }

        .dark .members-empty {
            border-color: rgb(63 63 70);
            background: rgb(31 31 35);
            color: rgb(161 161 170);
        }

        /* =========================================================
           RESPONSIVE
        ========================================================== */

        @media (max-width: 1200px) {
            .members-kpi-grid,
            .status-grid,
            .members-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 1000px) {
            .members-charts-grid {
                grid-template-columns: 1fr;
            }

            .members-chart-card-full {
                grid-column: auto;
            }

            .fees-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .members-dashboard-header {
                align-items: flex-start;
                flex-direction: column;
                gap: 1rem;
            }

            .members-dashboard-badge {
                align-self: flex-start;
            }

            .members-kpi-grid,
            .status-grid,
            .members-summary-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .members-kpi {
                min-height: 145px;
                padding: 1.25rem;
            }

            .members-kpi-value {
                font-size: 21px !important;
            }

            .members-section {
                margin-top: 1.75rem;
                border-radius: 10px;
            }

            .members-section-header {
                padding: 1.15rem;
            }

            .members-section-body {
                padding: 1.15rem;
            }

            .members-chart-card {
                min-height: 300px;
            }

            .members-chart-wrapper {
                height: 220px;
            }

            .members-chart-wrapper-small {
                height: 200px;
            }
        }

        /* =========================================================
           REDUCED MOTION
        ========================================================== */

        @media (prefers-reduced-motion: reduce) {
            * {
                scroll-behavior: auto !important;
            }
        }
    </style>

    @once
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endonce

    <div class="members-dashboard space-y-8">

        {{-- =====================================================
             HEADER
        ====================================================== --}}

        <div class="members-dashboard-header">

            <div>
                <div class="members-dashboard-title">
                    Gestione soci
                </div>

                <div class="members-dashboard-subtitle">
                    Panoramica delle iscrizioni, quote associative e rinnovi.
                </div>
            </div>

            <div class="members-dashboard-badge">
                <span class="members-dashboard-badge-dot"></span>
                Dati aggiornati
            </div>

        </div>


        {{-- =====================================================
             KPI
        ====================================================== --}}

        <div class="members-kpi-grid">

            {{-- SOCI TOTALI --}}

            <div class="members-kpi members-kpi-primary">

                <div class="members-kpi-top">

                    <div>
                        <div class="members-kpi-label">
                            Soci totali
                        </div>

                        <div class="members-kpi-value">
                            {{ number_format($totalMembers, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="members-kpi-icon">
                        <x-heroicon-o-users class="h-5 w-5" />
                    </div>

                </div>

                <div class="members-kpi-description">
                    Totale soci presenti in anagrafica
                </div>

            </div>


            {{-- NUOVI SOCI --}}

            <div class="members-kpi members-kpi-success">

                <div class="members-kpi-top">

                    <div>
                        <div class="members-kpi-label">
                            Nuovi questo mese
                        </div>

                        <div class="members-kpi-value">
                            {{ number_format($newMembersThisMonth, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="members-kpi-icon">
                        <x-heroicon-o-user-plus class="h-5 w-5" />
                    </div>

                </div>

                <div class="members-kpi-description">
                    Nuove iscrizioni registrate nel mese
                </div>

            </div>


            {{-- RINNOVI --}}

            <div class="members-kpi members-kpi-warning">

                <div class="members-kpi-top">

                    <div>
                        <div class="members-kpi-label">
                            Rinnovi prossimi 30 giorni
                        </div>

                        <div class="members-kpi-value">
                            {{ number_format($renewalsNext30Days, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="members-kpi-icon">
                        <x-heroicon-o-calendar-days class="h-5 w-5" />
                    </div>

                </div>

                <div class="members-kpi-description">
                    Soci da contattare per il rinnovo
                </div>

            </div>


            {{-- SCADUTI --}}

            <div class="members-kpi members-kpi-danger">

                <div class="members-kpi-top">

                    <div>
                        <div class="members-kpi-label">
                            Rinnovi scaduti
                        </div>

                        <div class="members-kpi-value">
                            {{ number_format($expiredRenewals, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="members-kpi-icon">
                        <x-heroicon-o-exclamation-triangle class="h-5 w-5" />
                    </div>

                </div>

                <div class="members-kpi-description">
                    Rinnovi che richiedono attenzione
                </div>

            </div>

        </div>


        {{-- =====================================================
             SITUAZIONE QUOTE
        ====================================================== --}}

        <div class="members-section">

            <div class="members-section-header">

                <div class="members-section-title-wrap">

                    <div class="members-section-icon">
                        <x-heroicon-o-banknotes class="h-5 w-5" />
                    </div>

                    <div>
                        <div class="members-section-title">
                            Situazione quote
                        </div>

                        <div class="members-section-subtitle">
                            Riepilogo economico delle quote associative
                        </div>
                    </div>

                </div>

            </div>


            <div class="members-section-body">

                <div class="fees-grid">

                    {{-- QUOTE TOTALI --}}

                    <div class="fee-card">

                        <div class="fee-card-label">
                            Quote annuali registrate
                        </div>

                        <div class="fee-card-value">
                            € {{ number_format($totalAnnualFees, 2, ',', '.') }}
                        </div>

                        <div class="fee-card-meta">
                            Valore complessivo registrato
                        </div>

                    </div>


                    {{-- INCASSATO --}}

                    <div class="fee-card fee-card-income">

                        <div class="fee-card-label">
                            Incassato nell'anno
                        </div>

                        <div class="fee-card-value">
                            € {{ number_format($totalFeesPaidThisYear, 2, ',', '.') }}
                        </div>

                        <div class="fee-card-meta">
                            Pagamenti ricevuti nell'anno corrente
                        </div>

                    </div>


                    {{-- SOCI PAGANTI --}}

                    <div class="fee-card">

                        <div class="fee-card-label">
                            Soci con pagamento quest'anno
                        </div>

                        <div class="fee-card-value">
                            {{ number_format($membersWithFeePaymentsThisYear, 0, ',', '.') }}
                        </div>

                        <div class="fee-card-meta">
                            Soci che hanno effettuato almeno un pagamento
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             GRAFICI
        ====================================================== --}}

        <div class="members-section">

            <div class="members-section-header">

                <div class="members-section-title-wrap">

                    <div class="members-section-icon">
                        <x-heroicon-o-chart-pie class="h-5 w-5" />
                    </div>

                    <div>
                        <div class="members-section-title">
                            Analisi soci e quote
                        </div>

                        <div class="members-section-subtitle">
                            Indicatori visivi per stato, rinnovi e situazione economica
                        </div>
                    </div>

                </div>

            </div>


            <div class="members-section-body">

                <div class="members-charts-grid">

                    {{-- STATO SOCI --}}

                    <div class="members-chart-card">

                        <div class="members-chart-title">
                            Distribuzione soci
                        </div>

                        <div class="members-chart-subtitle">
                            Soci suddivisi per stato
                        </div>

                        <div class="members-chart-wrapper">
                            <canvas id="membersStatusChart"></canvas>
                        </div>

                    </div>


                    {{-- QUOTE --}}

                    <div class="members-chart-card">

                        <div class="members-chart-title">
                            Situazione quote
                        </div>

                        <div class="members-chart-subtitle">
                            Confronto tra quote registrate e incassato
                        </div>

                        <div class="members-chart-wrapper">
                            <canvas id="membersFeesChart"></canvas>
                        </div>

                    </div>


                    {{-- RINNOVI --}}

                    <div class="members-chart-card members-chart-card-full">

                        <div class="members-chart-title">
                            Situazione rinnovi
                        </div>

                        <div class="members-chart-subtitle">
                            Rinnovi prossimi alla scadenza e rinnovi già scaduti
                        </div>

                        <div class="members-chart-wrapper-small">
                            <canvas id="membersRenewalsChart"></canvas>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             STATO SOCI
        ====================================================== --}}

        <div class="members-section">

            <div class="members-section-header">

                <div class="members-section-title-wrap">

                    <div class="members-section-icon">
                        <x-heroicon-o-users class="h-5 w-5" />
                    </div>

                    <div>
                        <div class="members-section-title">
                            Stato dei soci
                        </div>

                        <div class="members-section-subtitle">
                            Distribuzione dei soci per stato
                        </div>
                    </div>

                </div>

            </div>


            <div class="members-section-body">

                @if(count($membersByStatus))

                    <div class="status-grid">

                        @foreach($membersByStatus as $status => $total)

                            <div class="status-card">

                                <div class="status-left">

                                    <span class="status-indicator"></span>

                                    <span class="status-name">
                                        {{ $status ?: 'Non specificato' }}
                                    </span>

                                </div>

                                <div class="status-total">
                                    {{ number_format($total, 0, ',', '.') }}
                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="members-empty">

                        <x-heroicon-o-information-circle
                            class="mx-auto h-8 w-8 opacity-50"
                        />

                        <div class="mt-3 text-sm">
                            Nessun dato disponibile.
                        </div>

                    </div>

                @endif

            </div>

        </div>


        {{-- =====================================================
             RIEPILOGO
        ====================================================== --}}

        <div class="members-section">

            <div class="members-section-header">

                <div class="members-section-title-wrap">

                    <div class="members-section-icon">
                        <x-heroicon-o-clipboard-document-list class="h-5 w-5" />
                    </div>

                    <div>
                        <div class="members-section-title">
                            Riepilogo
                        </div>

                        <div class="members-section-subtitle">
                            Indicatori principali della gestione soci
                        </div>
                    </div>

                </div>

            </div>


            <div class="members-section-body">

                <div class="members-summary-grid">

                    <div class="members-summary-item">

                        <div class="members-summary-label">
                            Soci totali
                        </div>

                        <div class="members-summary-value">
                            {{ number_format($totalMembers, 0, ',', '.') }}
                        </div>

                    </div>


                    <div class="members-summary-item">

                        <div class="members-summary-label">
                            Nuovi questo mese
                        </div>

                        <div class="members-summary-value">
                            {{ number_format($newMembersThisMonth, 0, ',', '.') }}
                        </div>

                    </div>


                    <div class="members-summary-item">

                        <div class="members-summary-label">
                            Rinnovi prossimi
                        </div>

                        <div class="members-summary-value">
                            {{ number_format($renewalsNext30Days, 0, ',', '.') }}
                        </div>

                    </div>


                    <div class="members-summary-item">

                        <div class="members-summary-label">
                            Rinnovi scaduti
                        </div>

                        <div class="members-summary-value">
                            {{ number_format($expiredRenewals, 0, ',', '.') }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         CHART.JS
    ========================================================== --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const statusLabels = @json($statusLabels);
            const statusValues = @json($statusValues);

            const feesLabels = @json($feesLabels);
            const feesValues = @json($feesValues);

            const renewalsLabels = @json($renewalsLabels);
            const renewalsValues = @json($renewalsValues);

            const textColor = 'rgb(107 114 128)';
            const gridColor = 'rgb(229 231 235)';

            /*
            |--------------------------------------------------------------------------
            | STATO SOCI
            |--------------------------------------------------------------------------
            */

            const statusCanvas = document.getElementById('membersStatusChart');

            if (statusCanvas && statusValues.length) {

                new Chart(statusCanvas, {
                    type: 'doughnut',

                    data: {
                        labels: statusLabels,

                        datasets: [{
                            data: statusValues,

                            backgroundColor: [
                                'rgb(37 99 235)',
                                'rgb(22 163 74)',
                                'rgb(217 119 6)',
                                'rgb(220 38 38)',
                                'rgb(107 114 128)',
                            ],

                            borderWidth: 2,
                            borderColor: 'rgb(255 255 255)',
                        }],
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        plugins: {
                            legend: {
                                position: 'bottom',

                                labels: {
                                    color: textColor,
                                    usePointStyle: true,
                                    padding: 18,
                                    font: {
                                        size: 11,
                                    },
                                },
                            },
                        },

                        cutout: '62%',
                    },
                });
            }


            /*
            |--------------------------------------------------------------------------
            | QUOTE
            |--------------------------------------------------------------------------
            */

            const feesCanvas = document.getElementById('membersFeesChart');

            if (feesCanvas) {

                new Chart(feesCanvas, {
                    type: 'bar',

                    data: {
                        labels: feesLabels,

                        datasets: [{
                            label: 'Importo',

                            data: feesValues,

                            backgroundColor: [
                                'rgb(37 99 235)',
                                'rgb(22 163 74)',
                            ],

                            borderRadius: 6,
                            borderSkipped: false,
                        }],
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        plugins: {
                            legend: {
                                display: false,
                            },
                        },

                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                },

                                ticks: {
                                    color: textColor,
                                },
                            },

                            y: {
                                beginAtZero: true,

                                grid: {
                                    color: gridColor,
                                },

                                ticks: {
                                    color: textColor,

                                    callback: function (value) {
                                        return '€ ' + Number(value)
                                            .toLocaleString('it-IT');
                                    },
                                },
                            },
                        },
                    },
                });
            }


            /*
            |--------------------------------------------------------------------------
            | RINNOVI
            |--------------------------------------------------------------------------
            */

            const renewalsCanvas = document.getElementById('membersRenewalsChart');

            if (renewalsCanvas) {

                new Chart(renewalsCanvas, {
                    type: 'bar',

                    data: {
                        labels: renewalsLabels,

                        datasets: [{
                            label: 'Soci',

                            data: renewalsValues,

                            backgroundColor: [
                                'rgb(217 119 6)',
                                'rgb(220 38 38)',
                            ],

                            borderRadius: 6,
                            borderSkipped: false,
                        }],
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        plugins: {
                            legend: {
                                display: false,
                            },
                        },

                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                },

                                ticks: {
                                    color: textColor,
                                },
                            },

                            y: {
                                beginAtZero: true,

                                ticks: {
                                    precision: 0,
                                    color: textColor,
                                },

                                grid: {
                                    color: gridColor,
                                },
                            },
                        },
                    },
                });
            }

        });
    </script>

</x-filament-panels::page>