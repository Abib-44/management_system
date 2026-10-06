<?php

namespace App\Filament\Pages;

use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Pages\Page;

class FinanceDashboard extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.finance-dashboard';

    /*
    |--------------------------------------------------------------------------
    | KPI ESISTENTI
    |--------------------------------------------------------------------------
    */

    public float $incomeThisMonth = 0;

    public float $expensesThisMonth = 0;

    public float $balanceThisMonth = 0;

    public float $incomeThisYear = 0;

    public float $expensesThisYear = 0;

    public float $balanceThisYear = 0;

    /*
    |--------------------------------------------------------------------------
    | CATEGORIE ESISTENTI
    |--------------------------------------------------------------------------
    */

    public array $incomeByCategory = [];

    public array $expenseByCategory = [];

    /*
    |--------------------------------------------------------------------------
    | NUOVE STATISTICHE
    |--------------------------------------------------------------------------
    */

    public float $averageMonthlyIncome = 0;

    public float $averageMonthlyExpenses = 0;

    public float $savingsRate = 0;

    public int $transactionCount = 0;

    public float $topIncomeAmount = 0;

    public float $topExpenseAmount = 0;

    public string $topIncomeCategory = '';

    public string $topExpenseCategory = '';

    /*
    |--------------------------------------------------------------------------
    | NUOVI DATI GRAFICI
    |--------------------------------------------------------------------------
    */

    public array $weeklyChart = [
        'labels' => [],
        'income' => [],
        'expenses' => [],
    ];

    public array $monthlyChart = [
        'labels' => [],
        'income' => [],
        'expenses' => [],
    ];

    public array $yearlyChart = [
        'labels' => [],
        'income' => [],
        'expenses' => [],
    ];

    /*
    |--------------------------------------------------------------------------
    | PODIUM
    |--------------------------------------------------------------------------
    */

    public array $topIncomeCategories = [];

    public array $topExpenseCategories = [];

    /*
    |--------------------------------------------------------------------------
    | ULTIME TRANSAZIONI
    |--------------------------------------------------------------------------
    */

    public array $latestTransactions = [];

    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $this->loadDashboardData();
    }

    /*
    |--------------------------------------------------------------------------
    | CARICAMENTO DATI
    |--------------------------------------------------------------------------
    */

    protected function loadDashboardData(): void
    {
        $now = now();

        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $yearStart = $now->copy()->startOfYear();
        $yearEnd = $now->copy()->endOfYear();

        /*
        |--------------------------------------------------------------------------
        | ENTRATE / USCITE DEL MESE
        |--------------------------------------------------------------------------
        */

        $monthlyTotals = FinancialTransaction::query()
            ->whereBetween('transaction_date', [
                $monthStart,
                $monthEnd,
            ])
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $this->incomeThisMonth = (float) (
            $monthlyTotals['income'] ?? 0
        );

        $this->expensesThisMonth = (float) (
            $monthlyTotals['expense'] ?? 0
        );

        $this->balanceThisMonth =
            $this->incomeThisMonth -
            $this->expensesThisMonth;

        /*
        |--------------------------------------------------------------------------
        | ENTRATE / USCITE DELL'ANNO
        |--------------------------------------------------------------------------
        */

        $yearlyTotals = FinancialTransaction::query()
            ->whereBetween('transaction_date', [
                $yearStart,
                $yearEnd,
            ])
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $this->incomeThisYear = (float) (
            $yearlyTotals['income'] ?? 0
        );

        $this->expensesThisYear = (float) (
            $yearlyTotals['expense'] ?? 0
        );

        $this->balanceThisYear =
            $this->incomeThisYear -
            $this->expensesThisYear;

        /*
        |--------------------------------------------------------------------------
        | TASSO DI RISPARMIO
        |--------------------------------------------------------------------------
        */

        $this->savingsRate = $this->incomeThisYear > 0
            ? (
                $this->balanceThisYear /
                $this->incomeThisYear
            ) * 100
            : 0;

        /*
        |--------------------------------------------------------------------------
        | NUMERO TRANSAZIONI
        |--------------------------------------------------------------------------
        */

        $this->transactionCount = FinancialTransaction::query()
            ->whereBetween('transaction_date', [
                $yearStart,
                $yearEnd,
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | MEDIA MENSILE
        |--------------------------------------------------------------------------
        */

        $monthsPassed = max(1, $now->month);

        $this->averageMonthlyIncome =
            $this->incomeThisYear /
            $monthsPassed;

        $this->averageMonthlyExpenses =
            $this->expensesThisYear /
            $monthsPassed;

        /*
        |--------------------------------------------------------------------------
        | CATEGORIE
        |--------------------------------------------------------------------------
        */

        $this->loadCategoryData();

        /*
        |--------------------------------------------------------------------------
        | GRAFICI
        |--------------------------------------------------------------------------
        */

        $this->buildWeeklyChart();

        $this->buildMonthlyChart();

        $this->buildYearlyChart();

        /*
        |--------------------------------------------------------------------------
        | ULTIME TRANSAZIONI
        |--------------------------------------------------------------------------
        */

        $this->loadLatestTransactions();
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORIE
    |--------------------------------------------------------------------------
    */

    protected function loadCategoryData(): void
    {
        $yearStart = now()->startOfYear();
        $yearEnd = now()->endOfYear();

        $categories = FinancialCategory::query()
            ->withSum([
                'transactions as total_income' => fn ($query) => $query
                    ->where('type', 'income')
                    ->whereBetween('transaction_date', [
                        $yearStart,
                        $yearEnd,
                    ]),
            ], 'amount')
            ->withSum([
                'transactions as total_expense' => fn ($query) => $query
                    ->where('type', 'expense')
                    ->whereBetween('transaction_date', [
                        $yearStart,
                        $yearEnd,
                    ]),
            ], 'amount')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | ENTRATE PER CATEGORIA
        |--------------------------------------------------------------------------
        */

        $this->incomeByCategory = $categories
            ->filter(
                fn ($category) => (float) $category->total_income > 0
            )
            ->sortByDesc('total_income')
            ->mapWithKeys(
                fn ($category) => [
                    $category->name => (float) $category->total_income,
                ]
            )
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | USCITE PER CATEGORIA
        |--------------------------------------------------------------------------
        */

        $this->expenseByCategory = $categories
            ->filter(
                fn ($category) => (float) $category->total_expense > 0
            )
            ->sortByDesc('total_expense')
            ->mapWithKeys(
                fn ($category) => [
                    $category->name => (float) $category->total_expense,
                ]
            )
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | PODIUM ENTRATE
        |--------------------------------------------------------------------------
        */

        $this->topIncomeCategories = collect(
            $this->incomeByCategory
        )
            ->sortDesc()
            ->take(3)
            ->map(
                fn ($amount, $name) => [
                    'name' => $name,
                    'amount' => (float) $amount,
                ]
            )
            ->values()
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | PODIUM USCITE
        |--------------------------------------------------------------------------
        */

        $this->topExpenseCategories = collect(
            $this->expenseByCategory
        )
            ->sortDesc()
            ->take(3)
            ->map(
                fn ($amount, $name) => [
                    'name' => $name,
                    'amount' => (float) $amount,
                ]
            )
            ->values()
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | TOP ENTRATA
        |--------------------------------------------------------------------------
        */

        if (! empty($this->topIncomeCategories)) {
            $this->topIncomeCategory =
                $this->topIncomeCategories[0]['name'];

            $this->topIncomeAmount =
                $this->topIncomeCategories[0]['amount'];
        }

        /*
        |--------------------------------------------------------------------------
        | TOP USCITA
        |--------------------------------------------------------------------------
        */

        if (! empty($this->topExpenseCategories)) {
            $this->topExpenseCategory =
                $this->topExpenseCategories[0]['name'];

            $this->topExpenseAmount =
                $this->topExpenseCategories[0]['amount'];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GRAFICO SETTIMANALE
    |--------------------------------------------------------------------------
    */

    protected function buildWeeklyChart(): void
    {
        $start = now()
            ->copy()
            ->subDays(6)
            ->startOfDay();

        $end = now()
            ->copy()
            ->endOfDay();

        $transactions = FinancialTransaction::query()
            ->whereBetween('transaction_date', [
                $start,
                $end,
            ])
            ->get([
                'transaction_date',
                'type',
                'amount',
            ]);

        $labels = [];

        $income = [];

        $expenses = [];

        $days = [
            'Dom',
            'Lun',
            'Mar',
            'Mer',
            'Gio',
            'Ven',
            'Sab',
        ];

        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);

            $dateKey = $date->format('Y-m-d');

            $labels[] =
                $days[$date->dayOfWeek].
                ' '.
                $date->format('d');

            $dayTransactions = $transactions->filter(
                fn ($transaction) => Carbon::parse(
                    $transaction->transaction_date
                )->format('Y-m-d') === $dateKey
            );

            $income[] = round(
                (float) $dayTransactions
                    ->where('type', 'income')
                    ->sum('amount'),
                2
            );

            $expenses[] = round(
                (float) $dayTransactions
                    ->where('type', 'expense')
                    ->sum('amount'),
                2
            );
        }

        $this->weeklyChart = [
            'labels' => $labels,
            'income' => $income,
            'expenses' => $expenses,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | GRAFICO MENSILE
    |--------------------------------------------------------------------------
    */

    protected function buildMonthlyChart(): void
    {
        $year = now()->year;

        $transactions = FinancialTransaction::query()
            ->whereBetween('transaction_date', [
                now()->startOfYear(),
                now()->endOfYear(),
            ])
            ->get([
                'transaction_date',
                'type',
                'amount',
            ]);

        $labels = [
            'Gen',
            'Feb',
            'Mar',
            'Apr',
            'Mag',
            'Giu',
            'Lug',
            'Ago',
            'Set',
            'Ott',
            'Nov',
            'Dic',
        ];

        $income = [];

        $expenses = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthTransactions = $transactions->filter(
                fn ($transaction) => Carbon::parse(
                    $transaction->transaction_date
                )->month === $month
            );

            $income[] = round(
                (float) $monthTransactions
                    ->where('type', 'income')
                    ->sum('amount'),
                2
            );

            $expenses[] = round(
                (float) $monthTransactions
                    ->where('type', 'expense')
                    ->sum('amount'),
                2
            );
        }

        $this->monthlyChart = [
            'labels' => $labels,
            'income' => $income,
            'expenses' => $expenses,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | GRAFICO ANNUALE
    |--------------------------------------------------------------------------
    */

    protected function buildYearlyChart(): void
    {
        $firstYear = now()->year - 4;

        $transactions = FinancialTransaction::query()
            ->where(
                'transaction_date',
                '>=',
                Carbon::create(
                    $firstYear,
                    1,
                    1
                )->startOfDay()
            )
            ->get([
                'transaction_date',
                'type',
                'amount',
            ]);

        $labels = [];

        $income = [];

        $expenses = [];

        for (
            $year = $firstYear;
            $year <= now()->year;
            $year++
        ) {
            $labels[] = (string) $year;

            $yearTransactions = $transactions->filter(
                fn ($transaction) => Carbon::parse(
                    $transaction->transaction_date
                )->year === $year
            );

            $income[] = round(
                (float) $yearTransactions
                    ->where('type', 'income')
                    ->sum('amount'),
                2
            );

            $expenses[] = round(
                (float) $yearTransactions
                    ->where('type', 'expense')
                    ->sum('amount'),
                2
            );
        }

        $this->yearlyChart = [
            'labels' => $labels,
            'income' => $income,
            'expenses' => $expenses,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ULTIME TRANSAZIONI
    |--------------------------------------------------------------------------
    */

    protected function loadLatestTransactions(): void
    {
        $this->latestTransactions =
            FinancialTransaction::query()
                ->with('category')
                ->latest('transaction_date')
                ->latest('id')
                ->limit(8)
                ->get()
                ->map(
                    function (
                        FinancialTransaction $transaction
                    ) {
                        return [
                            'id' => $transaction->id,

                            'type' => $transaction->type,

                            'amount' => (float) $transaction->amount,

                            'date' => Carbon::parse(
                                $transaction->transaction_date
                            )->format('d/m/Y'),

                            'category' => $transaction
                                ->category
                                ?->name
                                ?? 'Senza categoria',
                        ];
                    }
                )
                ->toArray();
    }
}
