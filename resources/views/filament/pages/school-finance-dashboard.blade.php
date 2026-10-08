<x-filament-panels::page>
    <div class="finance-dashboard">
        <div class="finance-header">
            <div>
                <h1>Finanza scolastica</h1>
                <p>Panoramica finanziaria delle attività scolastiche</p>
            </div>
        </div>

        <div class="finance-kpi-grid">
            <div class="finance-card finance-card-success">
                <div class="finance-card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                    </svg>
                </div>
                <div class="finance-card-content">
                    <span class="finance-card-label">Entrate questo mese</span>
                    <span class="finance-card-value">€ {{ number_format($incomeThisMonth, 2, ',', '.') }}</span>
                    <span class="finance-card-description">Entrate scolastiche</span>
                </div>
            </div>

            <div class="finance-card finance-card-danger">
                <div class="finance-card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6" />
                    </svg>
                </div>
                <div class="finance-card-content">
                    <span class="finance-card-label">Uscite questo mese</span>
                    <span class="finance-card-value">€ {{ number_format($expensesThisMonth, 2, ',', '.') }}</span>
                    <span class="finance-card-description">Spese scolastiche</span>
                </div>
            </div>

            <div class="finance-card finance-card-primary">
                <div class="finance-card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                    </svg>
                </div>
                <div class="finance-card-content">
                    <span class="finance-card-label">Saldo questo mese</span>
                    <span class="finance-card-value">€ {{ number_format($balanceThisMonth, 2, ',', '.') }}</span>
                    <span class="finance-card-description">
                        {{ $balanceThisMonth >= 0 ? 'Saldo positivo' : 'Saldo negativo' }}
                    </span>
                </div>
            </div>

            <div class="finance-card finance-card-warning">
                <div class="finance-card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                        <circle cx="12" cy="12" r="9" />
                    </svg>
                </div>
                <div class="finance-card-content">
                    <span class="finance-card-label">Transazioni annuali</span>
                    <span class="finance-card-value">{{ number_format($transactionCount, 0, ',', '.') }}</span>
                    <span class="finance-card-description">Operazioni scolastiche</span>
                </div>
            </div>
        </div>

        <div class="finance-section">
            <div class="finance-section-header">
                <div>
                    <h2>Riepilogo finanziario scolastico</h2>
                    <p>Situazione finanziaria della scuola nell'anno corrente</p>
                </div>
            </div>

            <div class="finance-summary-grid">
                <div class="finance-summary-item">
                    <span class="finance-summary-label">Entrate annuali</span>
                    <span class="finance-summary-value finance-income">
                        € {{ number_format($incomeThisYear, 2, ',', '.') }}
                    </span>
                </div>

                <div class="finance-summary-item">
                    <span class="finance-summary-label">Uscite annuali</span>
                    <span class="finance-summary-value finance-expense">
                        € {{ number_format($expensesThisYear, 2, ',', '.') }}
                    </span>
                </div>

                <div class="finance-summary-item">
                    <span class="finance-summary-label">Saldo annuale</span>
                    <span class="finance-summary-value {{ $balanceThisYear >= 0 ? 'finance-income' : 'finance-expense' }}">
                        € {{ number_format($balanceThisYear, 2, ',', '.') }}
                    </span>
                </div>

                <div class="finance-summary-item">
                    <span class="finance-summary-label">Tasso di risparmio</span>
                    <span class="finance-summary-value">
                        {{ number_format($savingsRate, 1, ',', '.') }}%
                    </span>
                </div>
            </div>
        </div>

        <div class="finance-section">
            <div class="finance-section-header">
                <div>
                    <h2>Andamento finanziario</h2>
                    <p>Entrate e uscite scolastiche nel tempo</p>
                </div>

                <div class="finance-chart-selector">
                    <button type="button" class="chart-period-button active" data-period="weekly">Settimana</button>
                    <button type="button" class="chart-period-button" data-period="monthly">Mese</button>
                    <button type="button" class="chart-period-button" data-period="yearly">Anno</button>
                </div>
            </div>

            <div class="finance-chart-container">
                <canvas id="financeMainChart"></canvas>
            </div>
        </div>

        <div class="finance-grid-2">
            <div class="finance-section">
                <div class="finance-section-header">
                    <div>
                        <h2>Entrate per categoria</h2>
                        <p>Distribuzione delle entrate scolastiche</p>
                    </div>
                </div>

                <div class="finance-category-list">
                    @forelse($incomeByCategory as $category => $amount)
                        <div class="finance-category-item">
                            <div class="finance-category-info">
                                <span class="finance-category-name">{{ $category }}</span>
                                <span class="finance-category-amount">
                                    € {{ number_format($amount, 2, ',', '.') }}
                                </span>
                            </div>

                            <div class="finance-progress">
                                <div
                                    class="finance-progress-bar finance-progress-income"
                                    style="width: {{ $incomeThisYear > 0 ? min(($amount / $incomeThisYear) * 100, 100) : 0 }}%"
                                ></div>
                            </div>
                        </div>
                    @empty
                        <div class="finance-empty">
                            Nessuna entrata scolastica disponibile.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="finance-section">
                <div class="finance-section-header">
                    <div>
                        <h2>Uscite per categoria</h2>
                        <p>Distribuzione delle spese scolastiche</p>
                    </div>
                </div>

                <div class="finance-category-list">
                    @forelse($expenseByCategory as $category => $amount)
                        <div class="finance-category-item">
                            <div class="finance-category-info">
                                <span class="finance-category-name">{{ $category }}</span>
                                <span class="finance-category-amount">
                                    € {{ number_format($amount, 2, ',', '.') }}
                                </span>
                            </div>

                            <div class="finance-progress">
                                <div
                                    class="finance-progress-bar finance-progress-expense"
                                    style="width: {{ $expensesThisYear > 0 ? min(($amount / $expensesThisYear) * 100, 100) : 0 }}%"
                                ></div>
                            </div>
                        </div>
                    @empty
                        <div class="finance-empty">
                            Nessuna spesa scolastica disponibile.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="finance-grid-2">
            <div class="finance-section">
                <div class="finance-section-header">
                    <div>
                        <h2>Statistiche</h2>
                        <p>Indicatori finanziari scolastici</p>
                    </div>
                </div>

                <div class="finance-stats-list">
                    <div class="finance-stat-row">
                        <span>Media entrate mensili</span>
                        <strong>€ {{ number_format($averageMonthlyIncome, 2, ',', '.') }}</strong>
                    </div>

                    <div class="finance-stat-row">
                        <span>Media uscite mensili</span>
                        <strong>€ {{ number_format($averageMonthlyExpenses, 2, ',', '.') }}</strong>
                    </div>

                    <div class="finance-stat-row">
                        <span>Saldo annuale</span>
                        <strong class="{{ $balanceThisYear >= 0 ? 'finance-income' : 'finance-expense' }}">
                            € {{ number_format($balanceThisYear, 2, ',', '.') }}
                        </strong>
                    </div>

                    <div class="finance-stat-row">
                        <span>Tasso di risparmio</span>
                        <strong>{{ number_format($savingsRate, 1, ',', '.') }}%</strong>
                    </div>
                </div>
            </div>

            <div class="finance-section">
                <div class="finance-section-header">
                    <div>
                        <h2>Distribuzione uscite</h2>
                        <p>Principali categorie di spesa</p>
                    </div>
                </div>

                <div class="finance-doughnut-container">
                    <canvas id="financeExpenseChart"></canvas>
                </div>
            </div>
        </div>

        <div class="finance-grid-2">
            <div class="finance-section">
                <div class="finance-section-header">
                    <div>
                        <h2>Top categorie entrate</h2>
                        <p>Le principali fonti di entrata</p>
                    </div>
                </div>

                <div class="finance-ranking-list">
                    @forelse($topIncomeCategories as $index => $category)
                        <div class="finance-ranking-item">
                            <div class="finance-ranking-number">{{ $index + 1 }}</div>

                            <div class="finance-ranking-content">
                                <span class="finance-ranking-name">{{ $category['name'] }}</span>
                                <span class="finance-ranking-amount finance-income">
                                    € {{ number_format($category['amount'], 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="finance-empty">
                            Nessuna categoria disponibile.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="finance-section">
                <div class="finance-section-header">
                    <div>
                        <h2>Top categorie uscite</h2>
                        <p>Le principali categorie di spesa</p>
                    </div>
                </div>

                <div class="finance-ranking-list">
                    @forelse($topExpenseCategories as $index => $category)
                        <div class="finance-ranking-item">
                            <div class="finance-ranking-number">{{ $index + 1 }}</div>

                            <div class="finance-ranking-content">
                                <span class="finance-ranking-name">{{ $category['name'] }}</span>
                                <span class="finance-ranking-amount finance-expense">
                                    € {{ number_format($category['amount'], 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="finance-empty">
                            Nessuna categoria disponibile.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="finance-section">
            <div class="finance-section-header">
                <div>
                    <h2>Studenti con pagamenti in sospeso</h2>
                    <p>{{ $unpaidUsersCount }} studenti con saldo da versare</p>
                </div>
            </div>

            <div class="finance-transactions">
                @forelse($unpaidUsers as $user)
                    <div class="finance-transaction">
                        <div class="finance-transaction-icon transaction-expense">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <div class="finance-transaction-content">
                            <span class="finance-transaction-description">{{ $user['name'] }}</span>
                            <span class="finance-transaction-meta">{{ $user['email'] }}</span>
                        </div>

                        <div class="finance-transaction-amount finance-expense">
                            € {{ number_format($user['remaining'], 2, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div class="finance-empty">
                        Tutti gli studenti sono in regola con i pagamenti.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="finance-section">
            <div class="finance-section-header">
                <div>
                    <h2>Ultime transazioni scolastiche</h2>
                    <p>Le operazioni finanziarie scolastiche più recenti</p>
                </div>
            </div>

            <div class="finance-transactions">
                @forelse($latestTransactions as $transaction)
                    <div class="finance-transaction">
                        <div class="finance-transaction-icon {{ $transaction['type'] === 'income' ? 'transaction-income' : 'transaction-expense' }}">
                            @if($transaction['type'] === 'income')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                                </svg>
                            @endif
                        </div>

                        <div class="finance-transaction-content">
                            <span class="finance-transaction-description">
                                {{ $transaction['description'] ?: $transaction['category'] }}
                            </span>
                            <span class="finance-transaction-meta">
                                {{ $transaction['category'] }} · {{ $transaction['date'] }}
                            </span>
                        </div>

                        <div class="finance-transaction-amount {{ $transaction['type'] === 'income' ? 'finance-income' : 'finance-expense' }}">
                            {{ $transaction['type'] === 'income' ? '+' : '-' }}
                            € {{ number_format($transaction['amount'], 2, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div class="finance-empty">
                        Nessuna transazione scolastica disponibile.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        .finance-dashboard {
            width: 100%;
        }

        .finance-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .finance-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: rgb(24 24 27);
        }

        .finance-header p {
            margin: 6px 0 0;
            color: rgb(107 114 128);
            font-size: 14px;
        }

        .finance-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .finance-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 20px;
            border: 1px solid rgb(229 231 235);
            border-radius: 12px;
            background: white;
            min-width: 0;
        }

        .finance-card-icon {
            width: 44px;
            height: 44px;
            min-width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        .finance-card-icon svg {
            width: 23px;
            height: 23px;
        }

        .finance-card-content {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .finance-card-label {
            font-size: 13px;
            color: rgb(107 114 128);
            margin-bottom: 4px;
        }

        .finance-card-value {
            font-size: 21px;
            font-weight: 700;
            color: rgb(24 24 27);
        }

        .finance-card-description {
            margin-top: 4px;
            font-size: 12px;
            color: rgb(107 114 128);
        }

        .finance-card-success .finance-card-icon {
            color: rgb(22 163 74);
            background: rgb(240 253 244);
        }

        .finance-card-danger .finance-card-icon {
            color: rgb(220 38 38);
            background: rgb(254 242 242);
        }

        .finance-card-primary .finance-card-icon {
            color: rgb(37 99 235);
            background: rgb(239 246 255);
        }

        .finance-card-warning .finance-card-icon {
            color: rgb(217 119 6);
            background: rgb(255 251 235);
        }

        .finance-section {
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid rgb(229 231 235);
            border-radius: 12px;
            background: white;
        }

        .finance-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        .finance-section-header h2 {
            margin: 0;
            font-size: 17px;
            font-weight: 650;
            color: rgb(24 24 27);
        }

        .finance-section-header p {
            margin: 5px 0 0;
            font-size: 13px;
            color: rgb(107 114 128);
        }

        .finance-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .finance-summary-item {
            padding: 16px;
            border-radius: 10px;
            background: rgb(249 250 251);
        }

        .finance-summary-label {
            display: block;
            margin-bottom: 7px;
            font-size: 12px;
            color: rgb(107 114 128);
        }

        .finance-summary-value {
            font-size: 19px;
            font-weight: 700;
            color: rgb(24 24 27);
        }

        .finance-income {
            color: rgb(22 163 74) !important;
        }

        .finance-expense {
            color: rgb(220 38 38) !important;
        }

        .finance-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }

        .finance-chart-selector {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 4px;
            border-radius: 8px;
            background: rgb(243 244 246);
        }

        .chart-period-button {
            border: 0;
            border-radius: 6px;
            padding: 7px 12px;
            background: transparent;
            color: rgb(107 114 128);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s ease;
        }

        .chart-period-button:hover {
            color: rgb(24 24 27);
        }

        .chart-period-button.active {
            background: white;
            color: rgb(37 99 235);
            box-shadow: 0 1px 3px rgba(0, 0, 0, .08);
        }

        .finance-chart-container {
            position: relative;
            height: 330px;
        }

        .finance-doughnut-container {
            position: relative;
            height: 280px;
        }

        .finance-category-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .finance-category-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .finance-category-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .finance-category-name {
            font-size: 13px;
            color: rgb(55 65 81);
        }

        .finance-category-amount {
            font-size: 13px;
            font-weight: 650;
            color: rgb(24 24 27);
        }

        .finance-progress {
            width: 100%;
            height: 7px;
            overflow: hidden;
            border-radius: 999px;
            background: rgb(243 244 246);
        }

        .finance-progress-bar {
            height: 100%;
            border-radius: 999px;
        }

        .finance-progress-income {
            background: rgb(22 163 74);
        }

        .finance-progress-expense {
            background: rgb(220 38 38);
        }

        .finance-stats-list {
            display: flex;
            flex-direction: column;
        }

        .finance-stat-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 0;
            border-bottom: 1px solid rgb(229 231 235);
            font-size: 13px;
            color: rgb(75 85 99);
        }

        .finance-stat-row:first-child {
            padding-top: 0;
        }

        .finance-stat-row:last-child {
            padding-bottom: 0;
            border-bottom: 0;
        }

        .finance-stat-row strong {
            font-size: 14px;
            color: rgb(24 24 27);
        }

        .finance-ranking-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .finance-ranking-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 9px;
            background: rgb(249 250 251);
        }

        .finance-ranking-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            min-width: 30px;
            border-radius: 8px;
            background: white;
            color: rgb(107 114 128);
            font-size: 12px;
            font-weight: 700;
        }

        .finance-ranking-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
        }

        .finance-ranking-name {
            font-size: 13px;
            color: rgb(55 65 81);
        }

        .finance-ranking-amount {
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }

        .finance-transactions {
            display: flex;
            flex-direction: column;
        }

        .finance-transaction {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid rgb(229 231 235);
        }

        .finance-transaction:first-child {
            padding-top: 0;
        }

        .finance-transaction:last-child {
            padding-bottom: 0;
            border-bottom: 0;
        }

        .finance-transaction-icon {
            width: 38px;
            height: 38px;
            min-width: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
        }

        .finance-transaction-icon svg {
            width: 19px;
            height: 19px;
        }

        .transaction-income {
            color: rgb(22 163 74);
            background: rgb(240 253 244);
        }

        .transaction-expense {
            color: rgb(220 38 38);
            background: rgb(254 242 242);
        }

        .finance-transaction-content {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
            flex: 1;
        }

        .finance-transaction-description {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 13px;
            font-weight: 600;
            color: rgb(24 24 27);
        }

        .finance-transaction-meta {
            font-size: 12px;
            color: rgb(107 114 128);
        }

        .finance-transaction-amount {
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
        }

        .finance-empty {
            padding: 24px 0;
            text-align: center;
            font-size: 13px;
            color: rgb(107 114 128);
        }

        @media (max-width: 1100px) {
            .finance-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .finance-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .finance-grid-2 {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .finance-section-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .finance-chart-selector {
                width: 100%;
            }

            .chart-period-button {
                flex: 1;
            }

            .finance-chart-container {
                height: 260px;
            }

            .finance-doughnut-container {
                height: 240px;
            }
        }

        @media (max-width: 640px) {
            .finance-kpi-grid,
            .finance-summary-grid {
                grid-template-columns: 1fr;
            }

            .finance-card {
                padding: 16px;
            }

            .finance-section {
                padding: 16px;
            }

            .finance-transaction {
                align-items: flex-start;
            }

            .finance-transaction-amount {
                font-size: 13px;
            }
        }

        .dark .finance-header h1,
        .dark .finance-card-value,
        .dark .finance-section-header h2,
        .dark .finance-summary-value,
        .dark .finance-stat-row strong,
        .dark .finance-transaction-description,
        .dark .finance-category-amount {
            color: rgb(244 244 245);
        }

        .dark .finance-card,
        .dark .finance-section {
            background: rgb(24 24 27);
            border-color: rgb(63 63 70);
        }

        .dark .finance-header p,
        .dark .finance-section-header p,
        .dark .finance-card-label,
        .dark .finance-card-description,
        .dark .finance-summary-label,
        .dark .finance-category-name,
        .dark .finance-stat-row,
        .dark .finance-transaction-meta,
        .dark .finance-empty {
            color: rgb(161 161 170);
        }

        .dark .finance-summary-item,
        .dark .finance-ranking-item {
            background: rgb(39 39 42);
        }

        .dark .finance-stat-row,
        .dark .finance-transaction {
            border-color: rgb(63 63 70);
        }

        .dark .finance-progress {
            background: rgb(63 63 70);
        }

        .dark .finance-chart-selector {
            background: rgb(39 39 42);
        }

        .dark .chart-period-button.active {
            background: rgb(24 24 27);
        }

        .dark .finance-ranking-number {
            background: rgb(24 24 27);
            color: rgb(161 161 170);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const weeklyData = @json($weeklyChart);
            const monthlyData = @json($monthlyChart);
            const yearlyData = @json($yearlyChart);
            const expenseCategories = @json($expenseByCategory);

            const incomeColor = 'rgb(22, 163, 74)';
            const expenseColor = 'rgb(220, 38, 38)';
            const primaryColor = 'rgb(37, 99, 235)';
            const warningColor = 'rgb(217, 119, 6)';
            const textColor = 'rgb(107, 114, 128)';
            const gridColor = 'rgba(229, 231, 235, 0.8)';

            let currentPeriod = 'weekly';

            const mainCanvas = document.getElementById('financeMainChart');

            if (mainCanvas) {
                const mainChart = new Chart(mainCanvas, {
                    type: 'line',
                    data: {
                        labels: weeklyData.labels,
                        datasets: [
                            {
                                label: 'Entrate',
                                data: weeklyData.income,
                                borderColor: incomeColor,
                                backgroundColor: 'rgba(22, 163, 74, 0.08)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5
                            },
                            {
                                label: 'Uscite',
                                data: weeklyData.expenses,
                                borderColor: expenseColor,
                                backgroundColor: 'rgba(220, 38, 38, 0.08)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    color: textColor,
                                    usePointStyle: true,
                                    boxWidth: 8
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return context.dataset.label + ': € ' +
                                            Number(context.parsed.y).toLocaleString('it-IT', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            });
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: textColor
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: gridColor
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

                document.querySelectorAll('.chart-period-button').forEach(function (button) {
                    button.addEventListener('click', function () {
                        document.querySelectorAll('.chart-period-button').forEach(function (item) {
                            item.classList.remove('active');
                        });

                        this.classList.add('active');

                        currentPeriod = this.dataset.period;

                        const data = currentPeriod === 'weekly'
                            ? weeklyData
                            : currentPeriod === 'monthly'
                                ? monthlyData
                                : yearlyData;

                        mainChart.data.labels = data.labels;
                        mainChart.data.datasets[0].data = data.income;
                        mainChart.data.datasets[1].data = data.expenses;
                        mainChart.update();
                    });
                });
            }

            const expenseCanvas = document.getElementById('financeExpenseChart');

            if (expenseCanvas) {
                const labels = Object.keys(expenseCategories);
                const data = Object.values(expenseCategories);

                new Chart(expenseCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                data: data,
                                backgroundColor: [
                                    primaryColor,
                                    expenseColor,
                                    warningColor,
                                    incomeColor,
                                    'rgb(124, 58, 237)',
                                    'rgb(14, 116, 144)',
                                    'rgb(79, 70, 229)',
                                    'rgb(190, 24, 93)'
                                ],
                                borderWidth: 0
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
                                    color: textColor,
                                    usePointStyle: true,
                                    padding: 14,
                                    boxWidth: 8
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return context.label + ': € ' +
                                            Number(context.parsed).toLocaleString('it-IT', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            });
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-filament-panels::page>