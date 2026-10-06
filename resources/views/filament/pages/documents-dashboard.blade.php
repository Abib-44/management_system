<x-filament-panels::page>

    @php
        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $member = $this->getFilesForEntity('member');
        $student = $this->getFilesForEntity('student');
        $activity = $this->getFilesForEntity('activity');
        $transaction = $this->getFilesForEntity('transaction');

        $totalFiles = $this->getTotalFiles();
        $totalSize = $this->getTotalSize();
        $totalArchives = $this->getTotalArchives();
        $expiredArchives = $this->getExpiredArchives();

        $genericFiles = $this->getGenericFiles();

        $pdfFiles = $this->getPdfFiles();
        $imageFiles = $this->getImageFiles();
        $wordFiles = $this->getWordFiles();
        $excelFiles = $this->getExcelFiles();

        $activeArchives = $this->getActiveArchives();
        $archivedArchives = $this->getArchivedArchives();


        /*
        |--------------------------------------------------------------------------
        | CHART DATA
        |--------------------------------------------------------------------------
        */

        $entityChartLabels = [
            'Soci',
            'Studenti',
            'Attività',
            'Transazioni',
            'Generici',
        ];

        $entityChartValues = [
            $member['count'],
            $student['count'],
            $activity['count'],
            $transaction['count'],
            $genericFiles,
        ];

        $formatChartLabels = [
            'PDF',
            'Immagini',
            'Word',
            'Excel',
        ];

        $formatChartValues = [
            $pdfFiles,
            $imageFiles,
            $wordFiles,
            $excelFiles,
        ];

        $statusChartLabels = [
            'Attivi',
            'Archiviati',
            'Scaduti',
        ];

        $statusChartValues = [
            $activeArchives,
            $archivedArchives,
            $expiredArchives,
        ];
    @endphp


    <div class="documents-dashboard">

        {{-- ================================================================
             HEADER
        ================================================================= --}}

        <div class="documents-header">

            <div>
                <h1 class="documents-title">
                    GESTIONE DOCUMENTALE
                </h1>

                <p class="documents-subtitle">
                    Archivio documenti e stato della documentazione
                </p>
            </div>

            <div class="documents-header-icon">
                <x-heroicon-o-document-text />
            </div>

        </div>


        {{-- ================================================================
             KPI
        ================================================================= --}}

        <div class="documents-kpi-grid">

            {{-- FILE TOTALI --}}
            <div class="documents-card documents-card-primary">

                <div class="documents-card-icon">
                    <x-heroicon-o-document-duplicate />
                </div>

                <div class="documents-card-content">

                    <span class="documents-label">
                        File totali
                    </span>

                    <div class="documents-value">
                        {{ number_format($totalFiles, 0, ',', '.') }}
                    </div>

                </div>

            </div>


            {{-- SPAZIO UTILIZZATO --}}
            <div class="documents-card documents-card-success">

                <div class="documents-card-icon">
                    <x-heroicon-o-server-stack />
                </div>

                <div class="documents-card-content">

                    <span class="documents-label">
                        Spazio utilizzato
                    </span>

                    <div class="documents-value">
                        {{ $totalSize }}
                    </div>

                </div>

            </div>


            {{-- ARCHIVI --}}
            <div class="documents-card documents-card-warning">

                <div class="documents-card-icon">
                    <x-heroicon-o-archive-box />
                </div>

                <div class="documents-card-content">

                    <span class="documents-label">
                        Archivi documentali
                    </span>

                    <div class="documents-value">
                        {{ number_format($totalArchives, 0, ',', '.') }}
                    </div>

                </div>

            </div>


            {{-- SCADUTI --}}
            <div class="documents-card documents-card-danger">

                <div class="documents-card-icon">
                    <x-heroicon-o-exclamation-triangle />
                </div>

                <div class="documents-card-content">

                    <span class="documents-label">
                        Scaduti
                    </span>

                    <div class="documents-value">
                        {{ number_format($expiredArchives, 0, ',', '.') }}
                    </div>

                </div>

            </div>

        </div>


        {{-- ================================================================
             DOCUMENTI COLLEGATI
        ================================================================= --}}

        <div class="documents-section">

            <div class="documents-section-header">

                <div>
                    <h2>
                        Documenti collegati
                    </h2>

                    <p>
                        Distribuzione dei documenti per tipologia di collegamento
                    </p>
                </div>

                <div class="documents-section-icon documents-icon-primary">
                    <x-heroicon-o-link />
                </div>

            </div>


            <div class="documents-category-list">

                {{-- SOCI --}}
                <div class="documents-category-row">

                    <div class="documents-category-info">

                        <span class="documents-category-dot documents-dot-primary"></span>

                        <span>
                            Soci
                        </span>

                    </div>

                    <span class="documents-category-value">
                        {{ number_format($member['count'], 0, ',', '.') }}
                    </span>

                </div>


                {{-- STUDENTI --}}
                <div class="documents-category-row">

                    <div class="documents-category-info">

                        <span class="documents-category-dot documents-dot-success"></span>

                        <span>
                            Studenti
                        </span>

                    </div>

                    <span class="documents-category-value">
                        {{ number_format($student['count'], 0, ',', '.') }}
                    </span>

                </div>


                {{-- ATTIVITÀ --}}
                <div class="documents-category-row">

                    <div class="documents-category-info">

                        <span class="documents-category-dot documents-dot-warning"></span>

                        <span>
                            Attività
                        </span>

                    </div>

                    <span class="documents-category-value">
                        {{ number_format($activity['count'], 0, ',', '.') }}
                    </span>

                </div>


                {{-- TRANSAZIONI --}}
                <div class="documents-category-row">

                    <div class="documents-category-info">

                        <span class="documents-category-dot documents-dot-danger"></span>

                        <span>
                            Transazioni
                        </span>

                    </div>

                    <span class="documents-category-value">
                        {{ number_format($transaction['count'], 0, ',', '.') }}
                    </span>

                </div>


                {{-- GENERICI --}}
                <div class="documents-category-row">

                    <div class="documents-category-info">

                        <span class="documents-category-dot documents-dot-neutral"></span>

                        <span>
                            Generici
                        </span>

                    </div>

                    <span class="documents-category-value">
                        {{ number_format($genericFiles, 0, ',', '.') }}
                    </span>

                </div>

            </div>

        </div>


        {{-- ================================================================
             GRAFICI
        ================================================================= --}}

        <div class="documents-columns">


            {{-- DOCUMENTI PER COLLEGAMENTO --}}
            <div class="documents-section">

                <div class="documents-section-header">

                    <div>
                        <h2>
                            Distribuzione documenti
                        </h2>

                        <p>
                            Documenti per tipologia di collegamento
                        </p>
                    </div>

                    <div class="documents-section-icon documents-icon-primary">
                        <x-heroicon-o-chart-pie />
                    </div>

                </div>

                <div class="documents-chart-container">
                    <canvas id="documentsEntityChart"></canvas>
                </div>

            </div>


            {{-- FORMATI --}}
            <div class="documents-section">

                <div class="documents-section-header">

                    <div>
                        <h2>
                            Formati dei file
                        </h2>

                        <p>
                            Distribuzione dei documenti per formato
                        </p>
                    </div>

                    <div class="documents-section-icon documents-icon-success">
                        <x-heroicon-o-document />
                    </div>

                </div>

                <div class="documents-chart-container">
                    <canvas id="documentsFormatChart"></canvas>
                </div>

            </div>

        </div>


        {{-- ================================================================
             STATO ARCHIVI
        ================================================================= --}}

        <div class="documents-section">

            <div class="documents-section-header">

                <div>
                    <h2>
                        Stato degli archivi
                    </h2>

                    <p>
                        Situazione corrente degli archivi documentali
                    </p>
                </div>

                <div class="documents-section-icon documents-icon-warning">
                    <x-heroicon-o-archive-box />
                </div>

            </div>


            <div class="documents-status-layout">

                <div class="documents-chart-container documents-status-chart">
                    <canvas id="documentsStatusChart"></canvas>
                </div>


                <div class="documents-status-list">

                    {{-- ATTIVI --}}
                    <div class="documents-status-item">

                        <div class="documents-status-label">

                            <span class="documents-category-dot documents-dot-success"></span>

                            Attivi

                        </div>

                        <strong>
                            {{ number_format($activeArchives, 0, ',', '.') }}
                        </strong>

                    </div>


                    {{-- ARCHIVIATI --}}
                    <div class="documents-status-item">

                        <div class="documents-status-label">

                            <span class="documents-category-dot documents-dot-warning"></span>

                            Archiviati

                        </div>

                        <strong>
                            {{ number_format($archivedArchives, 0, ',', '.') }}
                        </strong>

                    </div>


                    {{-- SCADUTI --}}
                    <div class="documents-status-item">

                        <div class="documents-status-label">

                            <span class="documents-category-dot documents-dot-danger"></span>

                            Scaduti

                        </div>

                        <strong>
                            {{ number_format($expiredArchives, 0, ',', '.') }}
                        </strong>

                    </div>

                </div>

            </div>

        </div>


        {{-- ================================================================
             RIEPILOGO
        ================================================================= --}}

        <div class="documents-summary">

            <div class="documents-summary-item">

                <span>
                    File totali
                </span>

                <strong>
                    {{ number_format($totalFiles, 0, ',', '.') }}
                </strong>

            </div>


            <div class="documents-summary-item">

                <span>
                    Archivi
                </span>

                <strong>
                    {{ number_format($totalArchives, 0, ',', '.') }}
                </strong>

            </div>


            <div class="documents-summary-item">

                <span>
                    Archivi attivi
                </span>

                <strong class="documents-text-success">
                    {{ number_format($activeArchives, 0, ',', '.') }}
                </strong>

            </div>


            <div class="documents-summary-item">

                <span>
                    Scaduti
                </span>

                <strong class="documents-text-danger">
                    {{ number_format($expiredArchives, 0, ',', '.') }}
                </strong>

            </div>

        </div>

    </div>


    {{-- ================================================================
         CHART.JS
    ================================================================= --}}

    @once
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endonce


    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const textColor = 'rgb(107 114 128)';
            const gridColor = 'rgb(229 231 235)';


            /*
            |--------------------------------------------------------------------------
            | DOCUMENTI PER COLLEGAMENTO
            |--------------------------------------------------------------------------
            */

            const entityCanvas = document.getElementById(
                'documentsEntityChart'
            );

            if (entityCanvas) {

                new Chart(entityCanvas, {

                    type: 'doughnut',

                    data: {

                        labels: @json($entityChartLabels),

                        datasets: [{

                            data: @json($entityChartValues),

                            backgroundColor: [
                                'rgb(37 99 235)',
                                'rgb(22 163 74)',
                                'rgb(217 119 6)',
                                'rgb(220 38 38)',
                                'rgb(107 114 128)',
                            ],

                            borderWidth: 0,

                        }]

                    },

                    options: {

                        responsive: true,

                        maintainAspectRatio: false,

                        cutout: '68%',

                        plugins: {

                            legend: {

                                position: 'bottom',

                                labels: {

                                    color: textColor,

                                    padding: 18,

                                    usePointStyle: true,

                                    pointStyle: 'circle',

                                }

                            }

                        }

                    }

                });

            }


            /*
            |--------------------------------------------------------------------------
            | FORMATI
            |--------------------------------------------------------------------------
            */

            const formatCanvas = document.getElementById(
                'documentsFormatChart'
            );

            if (formatCanvas) {

                new Chart(formatCanvas, {

                    type: 'bar',

                    data: {

                        labels: @json($formatChartLabels),

                        datasets: [{

                            data: @json($formatChartValues),

                            backgroundColor: [

                                'rgb(220 38 38)',
                                'rgb(37 99 235)',
                                'rgb(107 114 128)',
                                'rgb(22 163 74)',

                            ],

                            borderRadius: 5,

                            borderSkipped: false,

                        }]

                    },

                    options: {

                        responsive: true,

                        maintainAspectRatio: false,

                        plugins: {

                            legend: {
                                display: false,
                            }

                        },

                        scales: {

                            x: {

                                grid: {
                                    display: false,
                                },

                                ticks: {
                                    color: textColor,
                                }

                            },

                            y: {

                                beginAtZero: true,

                                ticks: {

                                    color: textColor,

                                    precision: 0,

                                },

                                grid: {
                                    color: gridColor,
                                }

                            }

                        }

                    }

                });

            }


            /*
            |--------------------------------------------------------------------------
            | STATO ARCHIVI
            |--------------------------------------------------------------------------
            */

            const statusCanvas = document.getElementById(
                'documentsStatusChart'
            );

            if (statusCanvas) {

                new Chart(statusCanvas, {

                    type: 'doughnut',

                    data: {

                        labels: @json($statusChartLabels),

                        datasets: [{

                            data: @json($statusChartValues),

                            backgroundColor: [

                                'rgb(22 163 74)',
                                'rgb(217 119 6)',
                                'rgb(220 38 38)',

                            ],

                            borderWidth: 0,

                        }]

                    },

                    options: {

                        responsive: true,

                        maintainAspectRatio: false,

                        cutout: '68%',

                        plugins: {

                            legend: {

                                position: 'bottom',

                                labels: {

                                    color: textColor,

                                    padding: 18,

                                    usePointStyle: true,

                                    pointStyle: 'circle',

                                }

                            }

                        }

                    }

                });

            }

        });

    </script>


    {{-- ================================================================
         STYLE
    ================================================================= --}}

    <style>

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */

        .documents-dashboard {
            width: 100%;
        }


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .documents-header {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-bottom: 24px;
        }

        .documents-title {
            margin: 0;

            font-size: 20px;
            line-height: 1.3;

            font-weight: 700;

            color: rgb(17 24 39);

            letter-spacing: -0.025em;
        }

        .documents-subtitle {
            margin: 5px 0 0;

            font-size: 14px;

            color: rgb(107 114 128);
        }

        .documents-header-icon {
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

        .documents-header-icon svg {
            width: 22px;
            height: 22px;
        }


        /*
        |--------------------------------------------------------------------------
        | KPI
        |--------------------------------------------------------------------------
        */

        .documents-kpi-grid {
            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 16px;

            margin-bottom: 24px;
        }

        .documents-card {
            display: flex;

            align-items: center;

            gap: 14px;

            padding: 18px;

            background: rgb(255 255 255);

            border: 1px solid rgb(229 231 235);

            border-radius: 10px;
        }

        .documents-card-icon {
            display: flex;

            align-items: center;
            justify-content: center;

            width: 42px;
            height: 42px;

            flex-shrink: 0;

            border-radius: 10px;
        }

        .documents-card-icon svg {
            width: 21px;
            height: 21px;
        }

        .documents-card-content {
            display: flex;

            flex-direction: column;

            min-width: 0;
        }

        .documents-label {
            display: block;

            margin-bottom: 3px;

            font-size: 13px;

            line-height: 1.4;

            color: rgb(107 114 128);
        }

        /*
        |--------------------------------------------------------------------------
        | IMPORTANTE:
        | Forziamo la visibilità dei numeri KPI per evitare override Filament.
        |--------------------------------------------------------------------------
        */

        .documents-value {
            display: block !important;

            font-size: 23px !important;

            line-height: 1.2 !important;

            font-weight: 700 !important;

            color: rgb(17 24 39) !important;

            opacity: 1 !important;

            visibility: visible !important;
        }


        /*
        |--------------------------------------------------------------------------
        | KPI COLORS
        |--------------------------------------------------------------------------
        */

        .documents-card-success .documents-card-icon {
            background: rgb(240 253 244);

            color: rgb(22 163 74);
        }

        .documents-card-danger .documents-card-icon {
            background: rgb(254 242 242);

            color: rgb(220 38 38);
        }

        .documents-card-primary .documents-card-icon {
            background: rgb(239 246 255);

            color: rgb(37 99 235);
        }

        .documents-card-warning .documents-card-icon {
            background: rgb(255 251 235);

            color: rgb(217 119 6);
        }


        /*
        |--------------------------------------------------------------------------
        | SECTIONS
        |--------------------------------------------------------------------------
        */

        .documents-section {
            background: rgb(255 255 255);

            border: 1px solid rgb(229 231 235);

            border-radius: 10px;

            padding: 20px;

            margin-bottom: 24px;
        }

        .documents-section-header {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 16px;

            margin-bottom: 20px;
        }

        .documents-section-header h2 {
            margin: 0;

            font-size: 15px;

            line-height: 1.4;

            font-weight: 600;

            color: rgb(17 24 39);
        }

        .documents-section-header p {
            margin: 4px 0 0;

            font-size: 13px;

            color: rgb(107 114 128);
        }

        .documents-section-icon {
            display: flex;

            align-items: center;

            justify-content: center;

            width: 36px;
            height: 36px;

            flex-shrink: 0;

            border-radius: 8px;
        }

        .documents-section-icon svg {
            width: 19px;
            height: 19px;
        }


        /*
        |--------------------------------------------------------------------------
        | SECTION ICONS
        |--------------------------------------------------------------------------
        */

        .documents-icon-primary {
            background: rgb(239 246 255);

            color: rgb(37 99 235);
        }

        .documents-icon-success {
            background: rgb(240 253 244);

            color: rgb(22 163 74);
        }

        .documents-icon-warning {
            background: rgb(255 251 235);

            color: rgb(217 119 6);
        }


        /*
        |--------------------------------------------------------------------------
        | CATEGORIES
        |--------------------------------------------------------------------------
        */

        .documents-category-list {
            display: flex;

            flex-direction: column;
        }

        .documents-category-row {
            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 12px 0;

            border-bottom: 1px solid rgb(229 231 235);
        }

        .documents-category-row:first-child {
            padding-top: 0;
        }

        .documents-category-row:last-child {
            padding-bottom: 0;

            border-bottom: 0;
        }

        .documents-category-info {
            display: flex;

            align-items: center;

            gap: 10px;

            font-size: 14px;

            color: rgb(17 24 39);
        }

        .documents-category-value {
            font-size: 14px;

            font-weight: 600;

            color: rgb(17 24 39);
        }

        .documents-category-dot {
            width: 8px;
            height: 8px;

            flex-shrink: 0;

            border-radius: 999px;
        }

        .documents-dot-primary {
            background: rgb(37 99 235);
        }

        .documents-dot-success {
            background: rgb(22 163 74);
        }

        .documents-dot-warning {
            background: rgb(217 119 6);
        }

        .documents-dot-danger {
            background: rgb(220 38 38);
        }

        .documents-dot-neutral {
            background: rgb(107 114 128);
        }


        /*
        |--------------------------------------------------------------------------
        | COLUMNS
        |--------------------------------------------------------------------------
        */

        .documents-columns {
            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 24px;
        }

        .documents-columns .documents-section {
            margin-bottom: 24px;
        }


        /*
        |--------------------------------------------------------------------------
        | CHARTS
        |--------------------------------------------------------------------------
        */

        .documents-chart-container {
            position: relative;

            width: 100%;

            height: 280px;
        }

        .documents-status-layout {
            display: grid;

            grid-template-columns:
                minmax(250px, 360px)
                1fr;

            align-items: center;

            gap: 40px;
        }

        .documents-status-chart {
            height: 280px;
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        .documents-status-list {
            display: flex;

            flex-direction: column;

            gap: 0;
        }

        .documents-status-item {
            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 15px 0;

            border-bottom: 1px solid rgb(229 231 235);
        }

        .documents-status-item:first-child {
            padding-top: 0;
        }

        .documents-status-item:last-child {
            padding-bottom: 0;

            border-bottom: 0;
        }

        .documents-status-label {
            display: flex;

            align-items: center;

            gap: 10px;

            font-size: 14px;

            color: rgb(17 24 39);
        }

        .documents-status-item strong {
            font-size: 16px;

            color: rgb(17 24 39);
        }


        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        .documents-summary {
            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            background: rgb(255 255 255);

            border: 1px solid rgb(229 231 235);

            border-radius: 10px;

            margin-bottom: 24px;
        }

        .documents-summary-item {
            display: flex;

            flex-direction: column;

            gap: 5px;

            padding: 18px 20px;

            border-right: 1px solid rgb(229 231 235);
        }

        .documents-summary-item:last-child {
            border-right: 0;
        }

        .documents-summary-item span {
            font-size: 12px;

            color: rgb(107 114 128);
        }

        .documents-summary-item strong {
            font-size: 18px;

            color: rgb(17 24 39);
        }

        .documents-text-success {
            color: rgb(22 163 74) !important;
        }

        .documents-text-danger {
            color: rgb(220 38 38) !important;
        }


        /*
        |--------------------------------------------------------------------------
        | DARK MODE
        |--------------------------------------------------------------------------
        */

        .dark .documents-title,
        .dark .documents-section-header h2,
        .dark .documents-category-info,
        .dark .documents-category-value,
        .dark .documents-status-label,
        .dark .documents-status-item strong,
        .dark .documents-summary-item strong {
            color: rgb(244 244 245);
        }

        .dark .documents-value {
            color: rgb(244 244 245) !important;

            opacity: 1 !important;

            visibility: visible !important;
        }

        .dark .documents-subtitle,
        .dark .documents-label,
        .dark .documents-section-header p,
        .dark .documents-summary-item span {
            color: rgb(161 161 170);
        }

        .dark .documents-card,
        .dark .documents-section,
        .dark .documents-summary {
            background: rgb(24 24 27);

            border-color: rgb(63 63 70);
        }

        .dark .documents-category-row,
        .dark .documents-status-item,
        .dark .documents-summary-item {
            border-color: rgb(63 63 70);
        }


        /*
        |--------------------------------------------------------------------------
        | DARK KPI COLORS
        |--------------------------------------------------------------------------
        */

        .dark .documents-card-success .documents-card-icon {
            background: rgb(20 83 45 / 0.35);

            color: rgb(74 222 128);
        }

        .dark .documents-card-danger .documents-card-icon {
            background: rgb(127 29 29 / 0.35);

            color: rgb(248 113 113);
        }

        .dark .documents-card-primary .documents-card-icon {
            background: rgb(30 58 138 / 0.35);

            color: rgb(96 165 250);
        }

        .dark .documents-card-warning .documents-card-icon {
            background: rgb(120 53 15 / 0.35);

            color: rgb(251 191 36);
        }


        /*
        |--------------------------------------------------------------------------
        | DARK SECTION ICONS
        |--------------------------------------------------------------------------
        */

        .dark .documents-header-icon,
        .dark .documents-icon-primary {
            background: rgb(30 58 138 / 0.35);

            color: rgb(96 165 250);
        }

        .dark .documents-icon-success {
            background: rgb(20 83 45 / 0.35);

            color: rgb(74 222 128);
        }

        .dark .documents-icon-warning {
            background: rgb(120 53 15 / 0.35);

            color: rgb(251 191 36);
        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1024px) {

            .documents-kpi-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            .documents-columns {
                grid-template-columns: 1fr;

                gap: 0;
            }

            .documents-status-layout {
                grid-template-columns: 1fr;

                gap: 24px;
            }
        }


        @media (max-width: 640px) {

            .documents-kpi-grid {
                grid-template-columns: 1fr;
            }

            .documents-summary {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            .documents-summary-item {
                border-right: 0;

                border-bottom: 1px solid rgb(229 231 235);
            }

            .documents-summary-item:nth-child(odd) {
                border-right: 1px solid rgb(229 231 235);
            }

            .documents-summary-item:nth-last-child(-n + 2) {
                border-bottom: 0;
            }

            .documents-header {
                align-items: flex-start;
            }

            .documents-chart-container {
                height: 240px;
            }
        }


        @media (max-width: 400px) {

            .documents-summary {
                grid-template-columns: 1fr;
            }

            .documents-summary-item,
            .documents-summary-item:nth-child(odd) {
                border-right: 0;

                border-bottom: 1px solid rgb(229 231 235);
            }

            .documents-summary-item:last-child {
                border-bottom: 0;
            }
        }


        .dark .documents-summary-item {
            border-color: rgb(63 63 70);
        }

    </style>

</x-filament-panels::page>