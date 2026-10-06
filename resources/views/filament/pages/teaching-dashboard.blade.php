<x-filament-panels::page>

    @php
        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $studentsCount = $this->getStudentsCount();
        $classesCount = $this->getClassesCount();
        $lessonsThisMonth = $this->getLessonsThisMonth();
        $averageGrade = $this->getAverageGrade();

        $activeSchoolYears = $this->getActiveSchoolYearsCount();
        $gradesThisMonth = $this->getGradesThisMonth();
        $attendanceThisMonth = $this->getAttendanceThisMonth();


        /*
        |--------------------------------------------------------------------------
        | CHART DATA
        |--------------------------------------------------------------------------
        |
        | Questi grafici usano i dati già disponibili nel dashboard.
        | Non richiedono nuove query nel PHP.
        |
        */

        $activityLabels = [
            'Studenti',
            'Classi',
            'Lezioni',
            'Voti',
            'Presenze',
        ];

        $activityValues = [
            $studentsCount,
            $classesCount,
            $lessonsThisMonth,
            $gradesThisMonth,
            $attendanceThisMonth,
        ];


        $schoolDataLabels = [
            'Anni scolastici',
            'Classi',
            'Studenti',
        ];

        $schoolDataValues = [
            $activeSchoolYears,
            $classesCount,
            $studentsCount,
        ];


        $monthlyDataLabels = [
            'Lezioni',
            'Voti',
            'Presenze',
        ];

        $monthlyDataValues = [
            $lessonsThisMonth,
            $gradesThisMonth,
            $attendanceThisMonth,
        ];
    @endphp


    <div class="teaching-dashboard">


        {{-- ================================================================
             HEADER
        ================================================================= --}}

        <div class="teaching-header">

            <div>

                <h1 class="teaching-title">
                    DASHBOARD DIDATTICA
                </h1>

                <p class="teaching-subtitle">
                    Panoramica di studenti, classi, lezioni, voti e presenze
                </p>

            </div>


            <div class="teaching-header-icon">
                <x-heroicon-o-academic-cap />
            </div>

        </div>


        {{-- ================================================================
             KPI
        ================================================================= --}}

        <div class="teaching-kpi-grid">


            {{-- STUDENTI --}}
            <div class="teaching-card teaching-card-primary">

                <div class="teaching-card-icon">

                    <x-heroicon-o-academic-cap />

                </div>

                <div class="teaching-card-content">

                    <span class="teaching-label">
                        Studenti
                    </span>

                    <div class="teaching-value">
                        {{ number_format($studentsCount, 0, ',', '.') }}
                    </div>

                </div>

            </div>


            {{-- CLASSI --}}
            <div class="teaching-card teaching-card-primary">

                <div class="teaching-card-icon">

                    <x-heroicon-o-building-office-2 />

                </div>

                <div class="teaching-card-content">

                    <span class="teaching-label">
                        Classi
                    </span>

                    <div class="teaching-value">
                        {{ number_format($classesCount, 0, ',', '.') }}
                    </div>

                </div>

            </div>


            {{-- LEZIONI --}}
            <div class="teaching-card teaching-card-success">

                <div class="teaching-card-icon">

                    <x-heroicon-o-calendar-days />

                </div>

                <div class="teaching-card-content">

                    <span class="teaching-label">
                        Lezioni questo mese
                    </span>

                    <div class="teaching-value">
                        {{ number_format($lessonsThisMonth, 0, ',', '.') }}
                    </div>

                </div>

            </div>


            {{-- MEDIA VOTI --}}
            <div class="teaching-card teaching-card-warning">

                <div class="teaching-card-icon">

                    <x-heroicon-o-chart-bar />

                </div>

                <div class="teaching-card-content">

                    <span class="teaching-label">
                        Media voti
                    </span>

                    <div class="teaching-value">

                        {{ $averageGrade !== null
                            ? number_format($averageGrade, 2, ',', '.')
                            : '—'
                        }}

                    </div>

                </div>

            </div>


        </div>


        {{-- ================================================================
             ATTIVITÀ DIDATTICA
        ================================================================= --}}

        <div class="teaching-section">

            <div class="teaching-section-header">

                <div>

                    <h2>
                        Attività didattica
                    </h2>

                    <p>
                        Indicatori dell'attività scolastica
                    </p>

                </div>


                <div class="teaching-section-icon teaching-icon-primary">

                    <x-heroicon-o-book-open />

                </div>

            </div>


            <div class="teaching-activity-list">


                {{-- ANNI SCOLASTICI --}}
                <div class="teaching-activity-row">

                    <div class="teaching-activity-info">

                        <span class="teaching-dot teaching-dot-primary"></span>

                        <span>
                            Anni scolastici attivi
                        </span>

                    </div>

                    <strong>
                        {{ number_format($activeSchoolYears, 0, ',', '.') }}
                    </strong>

                </div>


                {{-- VOTI --}}
                <div class="teaching-activity-row">

                    <div class="teaching-activity-info">

                        <span class="teaching-dot teaching-dot-warning"></span>

                        <span>
                            Voti questo mese
                        </span>

                    </div>

                    <strong>
                        {{ number_format($gradesThisMonth, 0, ',', '.') }}
                    </strong>

                </div>


                {{-- PRESENZE --}}
                <div class="teaching-activity-row">

                    <div class="teaching-activity-info">

                        <span class="teaching-dot teaching-dot-success"></span>

                        <span>
                            Presenze questo mese
                        </span>

                    </div>

                    <strong>
                        {{ number_format($attendanceThisMonth, 0, ',', '.') }}
                    </strong>

                </div>


            </div>

        </div>


        {{-- ================================================================
             GRAFICI
        ================================================================= --}}

        <div class="teaching-columns">


            {{-- ATTIVITÀ GENERALE --}}
            <div class="teaching-section">

                <div class="teaching-section-header">

                    <div>

                        <h2>
                            Attività generale
                        </h2>

                        <p>
                            Distribuzione degli indicatori didattici
                        </p>

                    </div>

                    <div class="teaching-section-icon teaching-icon-primary">

                        <x-heroicon-o-chart-pie />

                    </div>

                </div>


                <div class="teaching-chart-container">

                    <canvas id="teachingActivityChart"></canvas>

                </div>

            </div>


            {{-- STRUTTURA SCOLASTICA --}}
            <div class="teaching-section">

                <div class="teaching-section-header">

                    <div>

                        <h2>
                            Struttura scolastica
                        </h2>

                        <p>
                            Anni, classi e studenti
                        </p>

                    </div>

                    <div class="teaching-section-icon teaching-icon-success">

                        <x-heroicon-o-building-office-2 />

                    </div>

                </div>


                <div class="teaching-chart-container">

                    <canvas id="teachingSchoolChart"></canvas>

                </div>

            </div>


        </div>


        {{-- ================================================================
             ATTIVITÀ DEL MESE
        ================================================================= --}}

        <div class="teaching-section">

            <div class="teaching-section-header">

                <div>

                    <h2>
                        Attività del mese
                    </h2>

                    <p>
                        Lezioni, valutazioni e presenze registrate
                    </p>

                </div>


                <div class="teaching-section-icon teaching-icon-warning">

                    <x-heroicon-o-calendar-days />

                </div>

            </div>


            <div class="teaching-month-layout">


                <div class="teaching-chart-container teaching-month-chart">

                    <canvas id="teachingMonthlyChart"></canvas>

                </div>


                <div class="teaching-month-list">


                    {{-- LEZIONI --}}
                    <div class="teaching-month-item">

                        <div class="teaching-month-label">

                            <span class="teaching-dot teaching-dot-success"></span>

                            Lezioni

                        </div>

                        <strong>
                            {{ number_format($lessonsThisMonth, 0, ',', '.') }}
                        </strong>

                    </div>


                    {{-- VOTI --}}
                    <div class="teaching-month-item">

                        <div class="teaching-month-label">

                            <span class="teaching-dot teaching-dot-warning"></span>

                            Voti

                        </div>

                        <strong>
                            {{ number_format($gradesThisMonth, 0, ',', '.') }}
                        </strong>

                    </div>


                    {{-- PRESENZE --}}
                    <div class="teaching-month-item">

                        <div class="teaching-month-label">

                            <span class="teaching-dot teaching-dot-primary"></span>

                            Presenze

                        </div>

                        <strong>
                            {{ number_format($attendanceThisMonth, 0, ',', '.') }}
                        </strong>

                    </div>


                    {{-- MEDIA --}}
                    <div class="teaching-month-item">

                        <div class="teaching-month-label">

                            <span class="teaching-dot teaching-dot-neutral"></span>

                            Media voti

                        </div>

                        <strong>

                            {{ $averageGrade !== null
                                ? number_format($averageGrade, 2, ',', '.')
                                : '—'
                            }}

                        </strong>

                    </div>


                </div>

            </div>

        </div>


        {{-- ================================================================
             RIEPILOGO
        ================================================================= --}}

        <div class="teaching-summary">


            <div class="teaching-summary-item">

                <span>
                    Studenti
                </span>

                <strong>
                    {{ number_format($studentsCount, 0, ',', '.') }}
                </strong>

            </div>


            <div class="teaching-summary-item">

                <span>
                    Classi
                </span>

                <strong>
                    {{ number_format($classesCount, 0, ',', '.') }}
                </strong>

            </div>


            <div class="teaching-summary-item">

                <span>
                    Lezioni
                </span>

                <strong class="teaching-text-success">
                    {{ number_format($lessonsThisMonth, 0, ',', '.') }}
                </strong>

            </div>


            <div class="teaching-summary-item">

                <span>
                    Media voti
                </span>

                <strong class="teaching-text-warning">

                    {{ $averageGrade !== null
                        ? number_format($averageGrade, 2, ',', '.')
                        : '—'
                    }}

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
            | ATTIVITÀ GENERALE
            |--------------------------------------------------------------------------
            */

            const activityCanvas = document.getElementById(
                'teachingActivityChart'
            );

            if (activityCanvas) {

                new Chart(activityCanvas, {

                    type: 'doughnut',

                    data: {

                        labels: @json($activityLabels),

                        datasets: [{

                            data: @json($activityValues),

                            backgroundColor: [

                                'rgb(37 99 235)',
                                'rgb(107 114 128)',
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


            /*
            |--------------------------------------------------------------------------
            | STRUTTURA SCOLASTICA
            |--------------------------------------------------------------------------
            */

            const schoolCanvas = document.getElementById(
                'teachingSchoolChart'
            );

            if (schoolCanvas) {

                new Chart(schoolCanvas, {

                    type: 'bar',

                    data: {

                        labels: @json($schoolDataLabels),

                        datasets: [{

                            data: @json($schoolDataValues),

                            backgroundColor: [

                                'rgb(217 119 6)',
                                'rgb(107 114 128)',
                                'rgb(37 99 235)',

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
            | ATTIVITÀ DEL MESE
            |--------------------------------------------------------------------------
            */

            const monthlyCanvas = document.getElementById(
                'teachingMonthlyChart'
            );

            if (monthlyCanvas) {

                new Chart(monthlyCanvas, {

                    type: 'bar',

                    data: {

                        labels: @json($monthlyDataLabels),

                        datasets: [{

                            data: @json($monthlyDataValues),

                            backgroundColor: [

                                'rgb(22 163 74)',
                                'rgb(217 119 6)',
                                'rgb(37 99 235)',

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

        .teaching-dashboard {
            width: 100%;
        }


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .teaching-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 24px;

        }

        .teaching-title {

            margin: 0;

            font-size: 20px;

            line-height: 1.3;

            font-weight: 700;

            color: rgb(17 24 39);

            letter-spacing: -0.025em;

        }

        .teaching-subtitle {

            margin: 5px 0 0;

            font-size: 14px;

            color: rgb(107 114 128);

        }

        .teaching-header-icon {

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

        .teaching-header-icon svg {

            width: 22px;

            height: 22px;

        }


        /*
        |--------------------------------------------------------------------------
        | KPI GRID
        |--------------------------------------------------------------------------
        */

        .teaching-kpi-grid {

            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 16px;

            margin-bottom: 24px;

        }


        /*
        |--------------------------------------------------------------------------
        | KPI CARD
        |--------------------------------------------------------------------------
        */

        .teaching-card {

            display: flex;

            align-items: center;

            gap: 14px;

            min-width: 0;

            padding: 18px;

            background: rgb(255 255 255);

            border: 1px solid rgb(229 231 235);

            border-radius: 10px;

        }


        .teaching-card-icon {

            display: flex;

            align-items: center;

            justify-content: center;

            width: 42px;

            height: 42px;

            flex-shrink: 0;

            border-radius: 10px;

        }


        .teaching-card-icon svg {

            width: 21px;

            height: 21px;

        }


        .teaching-card-content {

            display: flex;

            flex-direction: column;

            min-width: 0;

        }


        .teaching-label {

            display: block;

            margin-bottom: 3px;

            font-size: 13px;

            line-height: 1.4;

            color: rgb(107 114 128);

        }


        /*
        |--------------------------------------------------------------------------
        | KPI VALUE
        |--------------------------------------------------------------------------
        */

        .teaching-value {

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

        .teaching-card-primary .teaching-card-icon {

            background: rgb(239 246 255);

            color: rgb(37 99 235);

        }


        .teaching-card-success .teaching-card-icon {

            background: rgb(240 253 244);

            color: rgb(22 163 74);

        }


        .teaching-card-warning .teaching-card-icon {

            background: rgb(255 251 235);

            color: rgb(217 119 6);

        }


        /*
        |--------------------------------------------------------------------------
        | SECTIONS
        |--------------------------------------------------------------------------
        */

        .teaching-section {

            background: rgb(255 255 255);

            border: 1px solid rgb(229 231 235);

            border-radius: 10px;

            padding: 20px;

            margin-bottom: 24px;

        }


        .teaching-section-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 16px;

            margin-bottom: 20px;

        }


        .teaching-section-header h2 {

            margin: 0;

            font-size: 15px;

            line-height: 1.4;

            font-weight: 600;

            color: rgb(17 24 39);

        }


        .teaching-section-header p {

            margin: 4px 0 0;

            font-size: 13px;

            color: rgb(107 114 128);

        }


        .teaching-section-icon {

            display: flex;

            align-items: center;

            justify-content: center;

            width: 36px;

            height: 36px;

            flex-shrink: 0;

            border-radius: 8px;

        }


        .teaching-section-icon svg {

            width: 19px;

            height: 19px;

        }


        .teaching-icon-primary {

            background: rgb(239 246 255);

            color: rgb(37 99 235);

        }


        .teaching-icon-success {

            background: rgb(240 253 244);

            color: rgb(22 163 74);

        }


        .teaching-icon-warning {

            background: rgb(255 251 235);

            color: rgb(217 119 6);

        }


        /*
        |--------------------------------------------------------------------------
        | ACTIVITY LIST
        |--------------------------------------------------------------------------
        */

        .teaching-activity-list {

            display: flex;

            flex-direction: column;

        }


        .teaching-activity-row {

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 12px 0;

            border-bottom: 1px solid rgb(229 231 235);

        }


        .teaching-activity-row:first-child {

            padding-top: 0;

        }


        .teaching-activity-row:last-child {

            padding-bottom: 0;

            border-bottom: 0;

        }


        .teaching-activity-info {

            display: flex;

            align-items: center;

            gap: 10px;

            font-size: 14px;

            color: rgb(17 24 39);

        }


        .teaching-activity-row strong {

            font-size: 14px;

            font-weight: 600;

            color: rgb(17 24 39);

        }


        /*
        |--------------------------------------------------------------------------
        | DOTS
        |--------------------------------------------------------------------------
        */

        .teaching-dot {

            width: 8px;

            height: 8px;

            flex-shrink: 0;

            border-radius: 999px;

        }


        .teaching-dot-primary {

            background: rgb(37 99 235);

        }


        .teaching-dot-success {

            background: rgb(22 163 74);

        }


        .teaching-dot-warning {

            background: rgb(217 119 6);

        }


        .teaching-dot-danger {

            background: rgb(220 38 38);

        }


        .teaching-dot-neutral {

            background: rgb(107 114 128);

        }


        /*
        |--------------------------------------------------------------------------
        | COLUMNS
        |--------------------------------------------------------------------------
        */

        .teaching-columns {

            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 24px;

        }


        .teaching-columns .teaching-section {

            margin-bottom: 24px;

        }


        /*
        |--------------------------------------------------------------------------
        | CHARTS
        |--------------------------------------------------------------------------
        */

        .teaching-chart-container {

            position: relative;

            width: 100%;

            height: 280px;

        }


        /*
        |--------------------------------------------------------------------------
        | MONTH LAYOUT
        |--------------------------------------------------------------------------
        */

        .teaching-month-layout {

            display: grid;

            grid-template-columns:
                minmax(250px, 360px)
                1fr;

            align-items: center;

            gap: 40px;

        }


        .teaching-month-chart {

            height: 280px;

        }


        .teaching-month-list {

            display: flex;

            flex-direction: column;

        }


        .teaching-month-item {

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 15px 0;

            border-bottom: 1px solid rgb(229 231 235);

        }


        .teaching-month-item:first-child {

            padding-top: 0;

        }


        .teaching-month-item:last-child {

            padding-bottom: 0;

            border-bottom: 0;

        }


        .teaching-month-label {

            display: flex;

            align-items: center;

            gap: 10px;

            font-size: 14px;

            color: rgb(17 24 39);

        }


        .teaching-month-item strong {

            font-size: 16px;

            color: rgb(17 24 39);

        }


        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        .teaching-summary {

            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            background: rgb(255 255 255);

            border: 1px solid rgb(229 231 235);

            border-radius: 10px;

            margin-bottom: 24px;

        }


        .teaching-summary-item {

            display: flex;

            flex-direction: column;

            gap: 5px;

            padding: 18px 20px;

            border-right: 1px solid rgb(229 231 235);

        }


        .teaching-summary-item:last-child {

            border-right: 0;

        }


        .teaching-summary-item span {

            font-size: 12px;

            color: rgb(107 114 128);

        }


        .teaching-summary-item strong {

            font-size: 18px;

            color: rgb(17 24 39);

        }


        .teaching-text-success {

            color: rgb(22 163 74) !important;

        }


        .teaching-text-warning {

            color: rgb(217 119 6) !important;

        }


        /*
        |--------------------------------------------------------------------------
        | DARK MODE
        |--------------------------------------------------------------------------
        */

        .dark .teaching-title,
        .dark .teaching-section-header h2,
        .dark .teaching-activity-info,
        .dark .teaching-activity-row strong,
        .dark .teaching-month-label,
        .dark .teaching-month-item strong,
        .dark .teaching-summary-item strong {

            color: rgb(244 244 245);

        }


        .dark .teaching-value {

            color: rgb(244 244 245) !important;

            opacity: 1 !important;

            visibility: visible !important;

        }


        .dark .teaching-subtitle,
        .dark .teaching-label,
        .dark .teaching-section-header p,
        .dark .teaching-summary-item span {

            color: rgb(161 161 170);

        }


        .dark .teaching-card,
        .dark .teaching-section,
        .dark .teaching-summary {

            background: rgb(24 24 27);

            border-color: rgb(63 63 70);

        }


        .dark .teaching-activity-row,
        .dark .teaching-month-item,
        .dark .teaching-summary-item {

            border-color: rgb(63 63 70);

        }


        /*
        |--------------------------------------------------------------------------
        | DARK ICONS
        |--------------------------------------------------------------------------
        */

        .dark .teaching-card-primary .teaching-card-icon,
        .dark .teaching-icon-primary {

            background: rgb(30 58 138 / 0.35);

            color: rgb(96 165 250);

        }


        .dark .teaching-card-success .teaching-card-icon,
        .dark .teaching-icon-success {

            background: rgb(20 83 45 / 0.35);

            color: rgb(74 222 128);

        }


        .dark .teaching-card-warning .teaching-card-icon,
        .dark .teaching-icon-warning {

            background: rgb(120 53 15 / 0.35);

            color: rgb(251 191 36);

        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1024px) {

            .teaching-kpi-grid {

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

            }


            .teaching-columns {

                grid-template-columns: 1fr;

                gap: 0;

            }


            .teaching-month-layout {

                grid-template-columns: 1fr;

                gap: 24px;

            }

        }


        @media (max-width: 640px) {

            .teaching-kpi-grid {

                grid-template-columns: 1fr;

            }


            .teaching-summary {

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

            }


            .teaching-summary-item {

                border-right: 0;

                border-bottom: 1px solid rgb(229 231 235);

            }


            .teaching-summary-item:nth-child(odd) {

                border-right: 1px solid rgb(229 231 235);

            }


            .teaching-summary-item:nth-last-child(-n + 2) {

                border-bottom: 0;

            }


            .teaching-header {

                align-items: flex-start;

            }


            .teaching-chart-container {

                height: 240px;

            }

        }


        @media (max-width: 400px) {

            .teaching-summary {

                grid-template-columns: 1fr;

            }


            .teaching-summary-item,
            .teaching-summary-item:nth-child(odd) {

                border-right: 0;

                border-bottom: 1px solid rgb(229 231 235);

            }


            .teaching-summary-item:last-child {

                border-bottom: 0;

            }

        }


        .dark .teaching-summary-item {

            border-color: rgb(63 63 70);

        }

    </style>

</x-filament-panels::page>