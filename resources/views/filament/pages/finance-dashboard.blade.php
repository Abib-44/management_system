<x-filament-panels::page>
    <div class="finance-dashboard">

        {{-- =====================================================
             KPI ORIGINALI
        ====================================================== --}}

        <div class="finance-kpi-grid">

            <div class="finance-card finance-card-success">
                <div class="finance-card-icon">
                    <x-heroicon-o-arrow-trending-up />
                </div>

                <div class="finance-card-content">
                    <div class="finance-label">Entrate questo mese</div>
                    <div class="finance-value">
                        € {{ number_format($incomeThisMonth, 2, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="finance-card finance-card-danger">
                <div class="finance-card-icon">
                    <x-heroicon-o-arrow-trending-down />
                </div>

                <div class="finance-card-content">
                    <div class="finance-label">Uscite questo mese</div>
                    <div class="finance-value">
                        € {{ number_format($expensesThisMonth, 2, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="finance-card finance-card-primary">
                <div class="finance-card-icon">
                    <x-heroicon-o-banknotes />
                </div>

                <div class="finance-card-content">
                    <div class="finance-label">Saldo questo mese</div>
                    <div class="finance-value">
                        € {{ number_format($balanceThisMonth, 2, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="finance-card finance-card-warning">
                <div class="finance-card-icon">
                    <x-heroicon-o-calendar-days />
                </div>

                <div class="finance-card-content">
                    <div class="finance-label">Saldo annuale</div>
                    <div class="finance-value">
                        € {{ number_format($balanceThisYear, 2, ',', '.') }}
                    </div>
                </div>
            </div>

        </div>


        {{-- =====================================================
             RIEPILOGO ANNUALE ORIGINALE
        ====================================================== --}}

        <div class="finance-section">

            <div class="finance-section-header">
                <div>
                    <h2>Riepilogo annuale</h2>
                    <p>Situazione finanziaria dell'anno corrente</p>
                </div>

                <div class="finance-section-icon finance-icon-success">
                    <x-heroicon-o-chart-bar />
                </div>
            </div>

            <div class="finance-summary-grid">

                <div class="finance-summary-item">
                    <div class="finance-summary-top">
                        <span>Entrate</span>
                        <x-heroicon-o-arrow-up />
                    </div>

                    <strong class="finance-summary-income">
                        € {{ number_format($incomeThisYear, 2, ',', '.') }}
                    </strong>
                </div>

                <div class="finance-summary-item">
                    <div class="finance-summary-top">
                        <span>Uscite</span>
                        <x-heroicon-o-arrow-down />
                    </div>

                    <strong class="finance-summary-expense">
                        € {{ number_format($expensesThisYear, 2, ',', '.') }}
                    </strong>
                </div>

                <div class="finance-summary-item">
                    <div class="finance-summary-top">
                        <span>Saldo</span>
                        <x-heroicon-o-scale />
                    </div>

                    <strong>
                        € {{ number_format($balanceThisYear, 2, ',', '.') }}
                    </strong>
                </div>

            </div>
        </div>


        {{-- =====================================================
             CATEGORIE ORIGINALI
        ====================================================== --}}

        <div class="finance-columns">

            {{-- Entrate per categoria --}}
            <div class="finance-section">

                <div class="finance-section-header">
                    <div>
                        <h2>Entrate per categoria</h2>
                        <p>Distribuzione delle entrate dell'anno</p>
                    </div>

                    <div class="finance-section-icon finance-icon-success">
                        <x-heroicon-o-arrow-trending-up />
                    </div>
                </div>

                @if(count($incomeByCategory))
                    <div class="finance-category-list">

                        @foreach($incomeByCategory as $category => $amount)
                            <div class="finance-category-row">

                                <div class="finance-category-info">
                                    <span class="finance-category-dot finance-dot-success"></span>

                                    <span title="{{ $category }}">
                                        {{ $category }}
                                    </span>
                                </div>

                                <strong class="finance-category-income">
                                    € {{ number_format($amount, 2, ',', '.') }}
                                </strong>

                            </div>
                        @endforeach

                    </div>
                @else
                    <div class="finance-empty">
                        <x-heroicon-o-chart-bar />
                        <span>Nessuna entrata registrata</span>
                    </div>
                @endif

            </div>


            {{-- Uscite per categoria --}}
            <div class="finance-section">

                <div class="finance-section-header">
                    <div>
                        <h2>Uscite per categoria</h2>
                        <p>Distribuzione delle uscite dell'anno</p>
                    </div>

                    <div class="finance-section-icon finance-icon-danger">
                        <x-heroicon-o-arrow-trending-down />
                    </div>
                </div>

                @if(count($expenseByCategory))
                    <div class="finance-category-list">

                        @foreach($expenseByCategory as $category => $amount)
                            <div class="finance-category-row">

                                <div class="finance-category-info">
                                    <span class="finance-category-dot finance-dot-danger"></span>

                                    <span title="{{ $category }}">
                                        {{ $category }}
                                    </span>
                                </div>

                                <strong class="finance-category-expense">
                                    € {{ number_format($amount, 2, ',', '.') }}
                                </strong>

                            </div>
                        @endforeach

                    </div>
                @else
                    <div class="finance-empty">
                        <x-heroicon-o-chart-bar />
                        <span>Nessuna uscita registrata</span>
                    </div>
                @endif

            </div>

        </div>


        {{-- =====================================================
             NUOVE SEZIONI
             Tutto ciò che segue è aggiunto in fondo.
        ====================================================== --}}


        {{-- =====================================================
             STATISTICHE EXTRA
        ====================================================== --}}

        <div class="finance-new-grid finance-new-grid-4">

            {{-- Media entrate --}}
            <div class="finance-section finance-new-stat-card">

                <div class="finance-extra-stat">
                    <div class="finance-extra-stat-icon finance-highlight-success">
                        <x-heroicon-o-arrow-trending-up />
                    </div>

                    <div>
                        <div class="finance-label">
                            Media mensile entrate
                        </div>

                        <div class="finance-extra-value">
                            € {{ number_format($averageMonthlyIncome, 2, ',', '.') }}
                        </div>
                    </div>
                </div>

            </div>


            {{-- Media uscite --}}
            <div class="finance-section finance-new-stat-card">

                <div class="finance-extra-stat">
                    <div class="finance-extra-stat-icon finance-highlight-danger">
                        <x-heroicon-o-arrow-trending-down />
                    </div>

                    <div>
                        <div class="finance-label">
                            Media mensile uscite
                        </div>

                        <div class="finance-extra-value">
                            € {{ number_format($averageMonthlyExpenses, 2, ',', '.') }}
                        </div>
                    </div>
                </div>

            </div>


            {{-- Risparmio --}}
            <div class="finance-section finance-new-stat-card">

                <div class="finance-extra-stat">
                    <div class="finance-extra-stat-icon finance-highlight-primary">
                        <x-heroicon-o-percent-badge />
                    </div>

                    <div>
                        <div class="finance-label">
                            Tasso di risparmio
                        </div>

                        <div class="finance-extra-value">
                            {{ number_format($savingsRate, 1, ',', '.') }}%
                        </div>
                    </div>
                </div>

            </div>


            {{-- Transazioni --}}
            <div class="finance-section finance-new-stat-card">

                <div class="finance-extra-stat">
                    <div class="finance-extra-stat-icon finance-highlight-warning">
                        <x-heroicon-o-clipboard-document-list />
                    </div>

                    <div>
                        <div class="finance-label">
                            Transazioni quest'anno
                        </div>

                        <div class="finance-extra-value">
                            {{ number_format($transactionCount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

            </div>

        </div>


        {{-- =====================================================
             GRAFICO PRINCIPALE
        ====================================================== --}}

        <div class="finance-new-section finance-section">

            <div class="finance-section-header">

                <div>
                    <h2>Andamento finanziario</h2>
                    <p>Confronto tra entrate e uscite</p>
                </div>

                <div class="finance-chart-controls">

                    <select
                        id="finance-period-selector"
                        class="finance-chart-select"
                    >
                        <option value="weekly">Settimana</option>
                        <option value="monthly" selected>Mese</option>
                        <option value="yearly">Anno</option>
                    </select>

                </div>

            </div>

            <div class="finance-chart-container">
                <canvas id="finance-main-chart"></canvas>
            </div>

        </div>


        {{-- =====================================================
             TOP CATEGORIE + DISTRIBUZIONE
        ====================================================== --}}

        <div class="finance-new-grid finance-new-grid-2">

            {{-- Top categorie --}}
            <div class="finance-section">

                <div class="finance-section-header">
                    <div>
                        <h2>Top categorie</h2>
                        <p>Le categorie con maggiore impatto</p>
                    </div>

                    <div class="finance-section-icon finance-icon-success">
                        <x-heroicon-o-trophy />
                    </div>
                </div>


                <div class="finance-podium-list">

                    @forelse($topIncomeCategories as $index => $item)

                        <div class="finance-podium-row">

                            <div class="finance-podium-position">
                                {{ $index + 1 }}
                            </div>

                            <div class="finance-podium-content">

                                <div class="finance-podium-name">
                                    {{ $item['name'] }}
                                </div>

                                <div class="finance-podium-value finance-category-income">
                                    € {{ number_format($item['amount'], 2, ',', '.') }}
                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="finance-empty">
                            <x-heroicon-o-trophy />
                            <span>Nessuna categoria disponibile</span>
                        </div>

                    @endforelse

                </div>

            </div>


            {{-- Distribuzione uscite --}}
            <div class="finance-section">

                <div class="finance-section-header">

                    <div>
                        <h2>Distribuzione uscite</h2>
                        <p>Ripartizione delle spese per categoria</p>
                    </div>

                    <div class="finance-section-icon finance-icon-danger">
                        <x-heroicon-o-chart-pie />
                    </div>

                </div>

                <div class="finance-chart-container finance-chart-wrapper-small">
                    <canvas id="finance-expense-chart"></canvas>
                </div>

            </div>

        </div>


        {{-- =====================================================
             TOP ENTRATE / TOP USCITE
        ====================================================== --}}

        <div class="finance-new-grid finance-new-grid-2">

            {{-- Top entrata --}}
            <div class="finance-section">

                <div class="finance-section-header">

                    <div>
                        <h2>Categoria con più entrate</h2>
                        <p>La categoria che ha generato più ricavi</p>
                    </div>

                    <div class="finance-section-icon finance-icon-success">
                        <x-heroicon-o-arrow-up />
                    </div>

                </div>

                <div class="finance-highlight-list">

                    @if($topIncomeCategory)
                        <div class="finance-highlight-row">

                            <div class="finance-highlight-icon finance-highlight-success">
                                <x-heroicon-o-banknotes />
                            </div>

                            <div>
                                <div class="finance-highlight-name">
                                    {{ $topIncomeCategory }}
                                </div>

                                <div class="finance-highlight-value finance-category-income">
                                    € {{ number_format($topIncomeAmount, 2, ',', '.') }}
                                </div>
                            </div>

                        </div>
                    @else
                        <div class="finance-empty">
                            <span>Nessun dato disponibile</span>
                        </div>
                    @endif

                </div>

            </div>


            {{-- Top uscita --}}
            <div class="finance-section">

                <div class="finance-section-header">

                    <div>
                        <h2>Categoria con più uscite</h2>
                        <p>La categoria che ha generato più spese</p>
                    </div>

                    <div class="finance-section-icon finance-icon-danger">
                        <x-heroicon-o-arrow-down />
                    </div>

                </div>

                <div class="finance-highlight-list">

                    @if($topExpenseCategory)
                        <div class="finance-highlight-row">

                            <div class="finance-highlight-icon finance-highlight-danger">
                                <x-heroicon-o-banknotes />
                            </div>

                            <div>
                                <div class="finance-highlight-name">
                                    {{ $topExpenseCategory }}
                                </div>

                                <div class="finance-highlight-value finance-category-expense">
                                    € {{ number_format($topExpenseAmount, 2, ',', '.') }}
                                </div>
                            </div>

                        </div>
                    @else
                        <div class="finance-empty">
                            <span>Nessun dato disponibile</span>
                        </div>
                    @endif

                </div>

            </div>

        </div>


        {{-- =====================================================
             ULTIME TRANSAZIONI
        ====================================================== --}}

        <div class="finance-section finance-new-section">

            <div class="finance-section-header">

                <div>
                    <h2>Ultime transazioni</h2>
                    <p>Le operazioni finanziarie più recenti</p>
                </div>

                <div class="finance-section-icon finance-icon-success">
                    <x-heroicon-o-clock />
                </div>

            </div>


            @if(count($latestTransactions))

                <div class="finance-transaction-list">

                    @foreach($latestTransactions as $transaction)

                        <div class="finance-transaction-row">

                            <div
                                class="
                                    finance-transaction-icon
                                    {{ $transaction['type'] === 'income'
                                        ? 'finance-highlight-success'
                                        : 'finance-highlight-danger'
                                    }}
                                "
                            >
                                @if($transaction['type'] === 'income')
                                    <x-heroicon-o-arrow-down-left />
                                @else
                                    <x-heroicon-o-arrow-up-right />
                                @endif
                            </div>


                            <div class="finance-transaction-content">

                                <div class="finance-transaction-title">
                                    {{ $transaction['category'] ?? 'Senza categoria' }}
                                </div>

                                <div class="finance-transaction-date">
                                    {{ $transaction['date'] }}
                                </div>

                            </div>


                            <div
                                class="
                                    finance-transaction-amount
                                    {{ $transaction['type'] === 'income'
                                        ? 'finance-category-income'
                                        : 'finance-category-expense'
                                    }}
                                "
                            >
                                {{ $transaction['type'] === 'income' ? '+' : '-' }}
                                € {{ number_format($transaction['amount'], 2, ',', '.') }}
                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="finance-empty">
                    <x-heroicon-o-banknotes />
                    <span>Nessuna transazione recente</span>
                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
         CHART.JS
    ========================================================== --}}

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const chartData = {
                weekly: @json($weeklyChart),
                monthly: @json($monthlyChart),
                yearly: @json($yearlyChart),
            };


            const incomeColor = 'rgb(22, 163, 74)';
            const expenseColor = 'rgb(220, 38, 38)';
            const primaryColor = 'rgb(37, 99, 235)';
            const warningColor = 'rgb(217, 119, 6)';

            const textColor = 'rgb(107, 114, 128)';
            const gridColor = 'rgba(229, 231, 235, 0.8)';


            // =====================================================
            // GRAFICO PRINCIPALE
            // =====================================================

            const mainCanvas = document.getElementById('finance-main-chart');

            let mainChart = null;


            function createMainChart(period = 'monthly') {

                if (mainChart) {
                    mainChart.destroy();
                }

                const data = chartData[period];

                mainChart = new Chart(mainCanvas, {
                    type: 'line',

                    data: {
                        labels: data.labels,

                        datasets: [
                            {
                                label: 'Entrate',
                                data: data.income,

                                borderColor: incomeColor,
                                backgroundColor: 'rgba(22, 163, 74, 0.08)',

                                borderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5,

                                tension: 0.35,
                                fill: true,
                            },

                            {
                                label: 'Uscite',
                                data: data.expenses,

                                borderColor: expenseColor,
                                backgroundColor: 'rgba(220, 38, 38, 0.06)',

                                borderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5,

                                tension: 0.35,
                                fill: true,
                            }
                        ]
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },

                        plugins: {
                            legend: {
                                position: 'bottom',

                                labels: {
                                    usePointStyle: true,
                                    color: textColor,
                                    padding: 20,
                                }
                            },

                            tooltip: {
                                callbacks: {
                                    label: function (context) {

                                        const value = Number(context.raw || 0);

                                        return `${context.dataset.label}: € ${value.toLocaleString(
                                            'it-IT',
                                            {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }
                                        )}`;
                                    }
                                }
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

                                grid: {
                                    color: gridColor,
                                },

                                ticks: {
                                    color: textColor,

                                    callback: function (value) {
                                        return '€ ' + Number(value).toLocaleString('it-IT');
                                    }
                                }
                            }
                        }
                    }
                });
            }


            createMainChart('monthly');


            // =====================================================
            // SELECT PERIODO
            // =====================================================

            const periodSelector =
                document.getElementById('finance-period-selector');


            if (periodSelector) {

                periodSelector.addEventListener('change', function () {

                    createMainChart(this.value);

                });

            }


            // =====================================================
            // GRAFICO DISTRIBUZIONE USCITE
            // =====================================================

            const expenseCanvas =
                document.getElementById('finance-expense-chart');


            if (expenseCanvas) {

                const expenseLabels =
                    @json(array_keys($expenseByCategory));

                const expenseValues =
                    @json(array_values($expenseByCategory));


                if (expenseLabels.length > 0) {

                    new Chart(expenseCanvas, {

                        type: 'doughnut',

                        data: {
                            labels: expenseLabels,

                            datasets: [
                                {
                                    data: expenseValues,

                                    backgroundColor: [
                                        'rgba(220, 38, 38, 0.85)',
                                        'rgba(239, 68, 68, 0.75)',
                                        'rgba(248, 113, 113, 0.65)',
                                        'rgba(185, 28, 28, 0.75)',
                                        'rgba(254, 202, 202, 0.80)',
                                        'rgba(153, 27, 27, 0.70)',
                                    ],

                                    borderWidth: 0,
                                }
                            ]
                        },

                        options: {

                            responsive: true,
                            maintainAspectRatio: false,

                            cutout: '68%',

                            plugins: {

                                legend: {
                                    position: 'bottom',

                                    labels: {
                                        usePointStyle: true,
                                        color: textColor,
                                        padding: 14,
                                    }
                                },

                                tooltip: {

                                    callbacks: {

                                        label: function (context) {

                                            const value =
                                                Number(context.raw || 0);

                                            return `${context.label}: € ${value.toLocaleString(
                                                'it-IT',
                                                {
                                                    minimumFractionDigits: 2,
                                                    maximumFractionDigits: 2
                                                }
                                            )}`;
                                        }

                                    }

                                }

                            }

                        }

                    });

                }

            }

        });
    </script>


    {{-- =========================================================
         CSS ORIGINALE
         NON MODIFICATO
    ========================================================== --}}

    <style>
        .finance-dashboard {
            width: 100%;
            max-width: 100%;
        }

        /* =========================================================
           KPI
        ========================================================== */

        .finance-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .finance-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: 1rem;
            min-height: 120px;
            padding: 1.25rem;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.875rem;
            background: rgb(255 255 255);
            box-shadow:
                0 1px 2px rgb(0 0 0 / 0.04),
                0 1px 3px rgb(0 0 0 / 0.03);
            transition:
                transform 150ms ease,
                box-shadow 150ms ease,
                border-color 150ms ease;
        }

        .finance-card:hover {
            transform: translateY(-1px);
            box-shadow:
                0 4px 12px rgb(0 0 0 / 0.07),
                0 1px 3px rgb(0 0 0 / 0.04);
        }

        .finance-card-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 3rem;
            height: 3rem;
            border-radius: 0.75rem;
        }

        .finance-card-icon svg {
            width: 1.5rem;
            height: 1.5rem;
        }

        .finance-card-content {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .finance-label {
            color: rgb(107 114 128);
            font-size: 0.8125rem;
            line-height: 1.25rem;
        }

        .finance-value {
            color: rgb(17 24 39);
            font-size: 1.5rem;
            line-height: 2rem;
            letter-spacing: -0.025em;
        }


        /* =========================================================
           COLORI KPI
        ========================================================== */

        .finance-card-success .finance-card-icon {
            background: rgb(240 253 244);
            color: rgb(22 163 74);
        }

        .finance-card-danger .finance-card-icon {
            background: rgb(254 242 242);
            color: rgb(220 38 38);
        }

        .finance-card-primary .finance-card-icon {
            background: rgb(239 246 255);
            color: rgb(37 99 235);
        }

        .finance-card-warning .finance-card-icon {
            background: rgb(255 251 235);
            color: rgb(217 119 6);
        }


        /* =========================================================
           SEZIONI
        ========================================================== */

        .finance-section {
            overflow: hidden;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.875rem;
            background: rgb(255 255 255);
            box-shadow:
                0 1px 2px rgb(0 0 0 / 0.04);
        }

        .finance-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid rgb(229 231 235);
        }

        .finance-section-header h2 {
            margin: 0;
            color: rgb(17 24 39);
            font-size: 0.9375rem;
            font-weight: 600;
            line-height: 1.5rem;
        }

        .finance-section-header p {
            margin: 0.25rem 0 0;
            color: rgb(107 114 128);
            font-size: 0.8125rem;
            line-height: 1.25rem;
        }


        /* =========================================================
           RIEPILOGO
        ========================================================== */

        .finance-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0;
        }

        .finance-summary-item {
            padding: 1.25rem;
        }

        .finance-summary-item + .finance-summary-item {
            border-left: 1px solid rgb(229 231 235);
        }

        .finance-summary-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            color: rgb(107 114 128);
            font-size: 0.8125rem;
            line-height: 1.25rem;
        }

        .finance-summary-top svg {
            width: 1rem;
            height: 1rem;
        }

        .finance-summary-item strong {
            display: block;
            margin-top: 0.5rem;
            font-size: 1.5rem;
            line-height: 2rem;
            letter-spacing: -0.025em;
        }

        .finance-summary-income {
            color: rgb(22 163 74);
        }

        .finance-summary-expense {
            color: rgb(220 38 38);
        }


        /* =========================================================
           COLONNE
        ========================================================== */

        .finance-columns {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }


        /* =========================================================
           ICONA SEZIONE
        ========================================================== */

        .finance-section-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 0.625rem;
        }

        .finance-section-icon svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        .finance-icon-success {
            background: rgb(240 253 244);
            color: rgb(22 163 74);
        }

        .finance-icon-danger {
            background: rgb(254 242 242);
            color: rgb(220 38 38);
        }


        /* =========================================================
           CATEGORIE
        ========================================================== */

        .finance-category-list {
            padding: 0.5rem 1.25rem;
        }

        .finance-category-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            min-height: 3.25rem;
            border-bottom: 1px solid rgb(243 244 246);
        }

        .finance-category-row:last-child {
            border-bottom: 0;
        }

        .finance-category-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 0;
        }

        .finance-category-info span {
            overflow: hidden;
            color: rgb(55 65 81);
            font-size: 0.875rem;
            line-height: 1.25rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .finance-category-dot {
            width: 0.5rem;
            height: 0.5rem;
            flex-shrink: 0;
            border-radius: 9999px;
        }

        .finance-dot-success {
            background: rgb(34 197 94);
        }

        .finance-dot-danger {
            background: rgb(239 68 68);
        }

        .finance-category-income {
            flex-shrink: 0;
            color: rgb(22 163 74);
            font-size: 0.875rem;
        }

        .finance-category-expense {
            flex-shrink: 0;
            color: rgb(220 38 38);
            font-size: 0.875rem;
        }


        /* =========================================================
           EMPTY
        ========================================================== */

        .finance-empty {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 9rem;
            color: rgb(107 114 128);
            font-size: 0.875rem;
        }

        .finance-empty svg {
            width: 1.25rem;
            height: 1.25rem;
        }


        /* =========================================================
           DARK MODE
        ========================================================== */

        .dark .finance-card,
        .dark .finance-section {
            border-color: rgb(255 255 255 / 0.10);
            background: rgb(24 24 27);
            box-shadow:
                0 1px 2px rgb(0 0 0 / 0.20);
        }

        .dark .finance-label,
        .dark .finance-section-header p,
        .dark .finance-summary-top {
            color: rgb(161 161 170);
        }

        .dark .finance-value,
        .dark .finance-section-header h2 {
            color: rgb(250 250 250);
        }

        .dark .finance-section-header,
        .dark .finance-summary-item + .finance-summary-item {
            border-color: rgb(255 255 255 / 0.10);
        }

        .dark .finance-category-row {
            border-color: rgb(255 255 255 / 0.06);
        }

        .dark .finance-category-info span {
            color: rgb(212 212 216);
        }

        .dark .finance-card-success .finance-card-icon,
        .dark .finance-icon-success {
            background: rgb(20 83 45 / 0.35);
            color: rgb(74 222 128);
        }

        .dark .finance-card-danger .finance-card-icon,
        .dark .finance-icon-danger {
            background: rgb(127 29 29 / 0.35);
            color: rgb(248 113 113);
        }

        .dark .finance-card-primary .finance-card-icon {
            background: rgb(30 58 138 / 0.35);
            color: rgb(96 165 250);
        }

        .dark .finance-card-warning .finance-card-icon {
            background: rgb(120 53 15 / 0.35);
            color: rgb(251 191 36);
        }

        .dark .finance-empty {
            color: rgb(161 161 170);
        }


        /* =========================================================
           RESPONSIVE
        ========================================================== */

        @media (max-width: 1280px) {
            .finance-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 1024px) {
            .finance-columns {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .finance-kpi-grid {
                grid-template-columns: 1fr;
            }

            .finance-summary-grid {
                grid-template-columns: 1fr;
            }

            .finance-summary-item + .finance-summary-item {
                border-top: 1px solid rgb(229 231 235);
                border-left: 0;
            }

            .dark .finance-summary-item + .finance-summary-item {
                border-color: rgb(255 255 255 / 0.10);
            }

            .finance-card {
                min-height: 105px;
            }

            .finance-value {
                font-size: 1.3rem;
            }

            .finance-section-header {
                padding: 1rem;
            }

            .finance-category-list {
                padding: 0.5rem 1rem;
            }
        }


        /* =========================================================
           NUOVE SEZIONI
           Stesso linguaggio visivo del CSS originale
        ========================================================== */

        .finance-new-section {
            margin-top: 1.5rem;
        }

        .finance-new-grid {
            display: grid;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .finance-new-grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .finance-new-grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .finance-new-stat-card {
            min-height: 100px;
        }

        .finance-extra-stat {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-height: 100px;
            padding: 1.25rem;
        }

        .finance-extra-stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.75rem;
            height: 2.75rem;
            flex-shrink: 0;
            border-radius: 0.75rem;
        }

        .finance-extra-stat-icon svg {
            width: 1.35rem;
            height: 1.35rem;
        }

        .finance-highlight-success {
            background: rgb(240 253 244);
            color: rgb(22 163 74);
        }

        .finance-highlight-danger {
            background: rgb(254 242 242);
            color: rgb(220 38 38);
        }

        .finance-highlight-primary {
            background: rgb(239 246 255);
            color: rgb(37 99 235);
        }

        .finance-highlight-warning {
            background: rgb(255 251 235);
            color: rgb(217 119 6);
        }

        .finance-extra-value {
            margin-top: 0.25rem;
            color: rgb(17 24 39);
            font-size: 1.25rem;
            line-height: 1.75rem;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .finance-chart-controls {
            display: flex;
            align-items: center;
        }

        .finance-chart-select {
            min-width: 120px;
            padding: 0.5rem 2rem 0.5rem 0.75rem;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.625rem;
            background: rgb(255 255 255);
            color: rgb(55 65 81);
            font-size: 0.8125rem;
            line-height: 1.25rem;
            outline: none;
        }

        .finance-chart-select:focus {
            border-color: rgb(37 99 235);
            box-shadow: 0 0 0 2px rgb(37 99 235 / 0.10);
        }

        .finance-chart-container {
            position: relative;
            width: 100%;
            height: 350px;
            padding: 1.25rem;
        }

        .finance-chart-wrapper-small {
            height: 330px;
        }

        .finance-podium-list {
            padding: 0.5rem 1.25rem 1rem;
        }

        .finance-podium-row {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            min-height: 4rem;
            border-bottom: 1px solid rgb(243 244 246);
        }

        .finance-podium-row:last-child {
            border-bottom: 0;
        }

        .finance-podium-position {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            flex-shrink: 0;
            border-radius: 0.5rem;
            background: rgb(243 244 246);
            color: rgb(107 114 128);
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .finance-podium-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            width: 100%;
            min-width: 0;
        }

        .finance-podium-name {
            overflow: hidden;
            color: rgb(55 65 81);
            font-size: 0.875rem;
            line-height: 1.25rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .finance-podium-value {
            flex-shrink: 0;
        }

        .finance-highlight-list {
            padding: 0.5rem 1.25rem 1.25rem;
        }

        .finance-highlight-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-height: 5rem;
        }

        .finance-highlight-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            flex-shrink: 0;
            border-radius: 0.75rem;
        }

        .finance-highlight-icon svg {
            width: 1.4rem;
            height: 1.4rem;
        }

        .finance-highlight-name {
            color: rgb(55 65 81);
            font-size: 0.9375rem;
            font-weight: 500;
            line-height: 1.4rem;
        }

        .finance-highlight-value {
            margin-top: 0.25rem;
        }

        .finance-transaction-list {
            padding: 0.5rem 1.25rem;
        }

        .finance-transaction-row {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            min-height: 4.25rem;
            border-bottom: 1px solid rgb(243 244 246);
        }

        .finance-transaction-row:last-child {
            border-bottom: 0;
        }

        .finance-transaction-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            flex-shrink: 0;
            border-radius: 0.625rem;
        }

        .finance-transaction-icon svg {
            width: 1.2rem;
            height: 1.2rem;
        }

        .finance-transaction-content {
            min-width: 0;
            flex: 1;
        }

        .finance-transaction-title {
            overflow: hidden;
            color: rgb(55 65 81);
            font-size: 0.875rem;
            line-height: 1.25rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .finance-transaction-date {
            margin-top: 0.15rem;
            color: rgb(107 114 128);
            font-size: 0.75rem;
            line-height: 1rem;
        }

        .finance-transaction-amount {
            flex-shrink: 0;
            font-size: 0.875rem;
            font-weight: 600;
        }


        /* =========================================================
           DARK MODE - NUOVE SEZIONI
        ========================================================== */

        .dark .finance-extra-value {
            color: rgb(250 250 250);
        }

        .dark .finance-chart-select {
            border-color: rgb(255 255 255 / 0.10);
            background: rgb(24 24 27);
            color: rgb(212 212 216);
        }

        .dark .finance-podium-row,
        .dark .finance-transaction-row {
            border-color: rgb(255 255 255 / 0.06);
        }

        .dark .finance-podium-position {
            background: rgb(39 39 42);
            color: rgb(161 161 170);
        }

        .dark .finance-podium-name,
        .dark .finance-highlight-name,
        .dark .finance-transaction-title {
            color: rgb(212 212 216);
        }

        .dark .finance-transaction-date {
            color: rgb(161 161 170);
        }

        .dark .finance-highlight-success {
            background: rgb(20 83 45 / 0.35);
            color: rgb(74 222 128);
        }

        .dark .finance-highlight-danger {
            background: rgb(127 29 29 / 0.35);
            color: rgb(248 113 113);
        }

        .dark .finance-highlight-primary {
            background: rgb(30 58 138 / 0.35);
            color: rgb(96 165 250);
        }

        .dark .finance-highlight-warning {
            background: rgb(120 53 15 / 0.35);
            color: rgb(251 191 36);
        }


        /* =========================================================
           RESPONSIVE - NUOVE SEZIONI
        ========================================================== */

        @media (max-width: 1280px) {
            .finance-new-grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 1024px) {
            .finance-new-grid-2 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .finance-new-grid-4 {
                grid-template-columns: 1fr;
            }

            .finance-chart-container {
                height: 280px;
                padding: 1rem;
            }

            .finance-chart-wrapper-small {
                height: 280px;
            }

            .finance-section-header {
                flex-wrap: wrap;
            }

            .finance-chart-controls {
                width: 100%;
            }

            .finance-chart-select {
                width: 100%;
            }

            .finance-podium-list,
            .finance-highlight-list,
            .finance-transaction-list {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .finance-transaction-amount {
                font-size: 0.8125rem;
            }
        }
    </style>
</x-filament-panels::page>