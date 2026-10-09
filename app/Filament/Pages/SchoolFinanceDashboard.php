<?php

namespace App\Filament\Pages;

use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Models\Student;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Pages\Page;

class SchoolFinanceDashboard extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.school-finance-dashboard';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationLabel = 'Finanza scolastica';

    protected static ?string $title = 'Finanza scolastica';

    protected static ?string $slug = 'school-finance';

    public float $incomeThisMonth = 0;

    public float $expensesThisMonth = 0;

    public float $balanceThisMonth = 0;

    public float $incomeThisYear = 0;

    public float $expensesThisYear = 0;

    public float $balanceThisYear = 0;

    public array $incomeByCategory = [];

    public array $expenseByCategory = [];

    public float $averageMonthlyIncome = 0;

    public float $averageMonthlyExpenses = 0;

    public float $savingsRate = 0;

    public int $transactionCount = 0;

    public float $topIncomeAmount = 0;

    public float $topExpenseAmount = 0;

    public string $topIncomeCategory = '';

    public string $topExpenseCategory = '';

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

    public array $topIncomeCategories = [];

    public array $topExpenseCategories = [];

    public array $latestTransactions = [];

    public array $unpaidUsers = [];

    public int $unpaidUsersCount = 0;

    public function mount(): void
    {
        $this->loadDashboardData();
    }

    protected function schoolTransactionsQuery()
    {
        return FinancialTransaction::query()->where('scope', 'school');
    }

    protected function loadDashboardData(): void
    {
        $now = now();

        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $yearStart = $now->copy()->startOfYear();
        $yearEnd = $now->copy()->endOfYear();

        $monthlyTotals = $this->schoolTransactionsQuery()
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $this->incomeThisMonth = (float) ($monthlyTotals['income'] ?? 0);
        $this->expensesThisMonth = (float) ($monthlyTotals['expense'] ?? 0);
        $this->balanceThisMonth = $this->incomeThisMonth - $this->expensesThisMonth;

        $yearlyTotals = $this->schoolTransactionsQuery()
            ->whereBetween('transaction_date', [$yearStart, $yearEnd])
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $this->incomeThisYear = (float) ($yearlyTotals['income'] ?? 0);
        $this->expensesThisYear = (float) ($yearlyTotals['expense'] ?? 0);
        $this->balanceThisYear = $this->incomeThisYear - $this->expensesThisYear;

        $this->savingsRate = $this->incomeThisYear > 0
            ? ($this->balanceThisYear / $this->incomeThisYear) * 100
            : 0;

        $this->transactionCount = $this->schoolTransactionsQuery()
            ->whereBetween('transaction_date', [$yearStart, $yearEnd])
            ->count();

        $monthsPassed = max(1, $now->month);

        $this->averageMonthlyIncome = $this->incomeThisYear / $monthsPassed;
        $this->averageMonthlyExpenses = $this->expensesThisYear / $monthsPassed;

        $this->loadCategoryData();
        $this->buildWeeklyChart();
        $this->buildMonthlyChart();
        $this->buildYearlyChart();
        $this->loadLatestTransactions();
        $this->loadUnpaidUsers();
    }

    protected function loadCategoryData(): void
    {
        $yearStart = now()->startOfYear();
        $yearEnd = now()->endOfYear();

        $categories = FinancialCategory::query()
            ->withSum([
                'transactions as total_income' => fn ($query) => $query
                    ->where('type', 'income')
                    ->where('scope', 'school')
                    ->whereBetween('transaction_date', [$yearStart, $yearEnd]),
            ], 'amount')
            ->withSum([
                'transactions as total_expense' => fn ($query) => $query
                    ->where('type', 'expense')
                    ->where('scope', 'school')
                    ->whereBetween('transaction_date', [$yearStart, $yearEnd]),
            ], 'amount')
            ->get();

        $this->incomeByCategory = $categories
            ->filter(fn ($category) => (float) $category->total_income > 0)
            ->sortByDesc('total_income')
            ->mapWithKeys(fn ($category) => [
                $category->name => (float) $category->total_income,
            ])
            ->toArray();

        $this->expenseByCategory = $categories
            ->filter(fn ($category) => (float) $category->total_expense > 0)
            ->sortByDesc('total_expense')
            ->mapWithKeys(fn ($category) => [
                $category->name => (float) $category->total_expense,
            ])
            ->toArray();

        $this->topIncomeCategories = collect($this->incomeByCategory)
            ->sortDesc()
            ->take(3)
            ->map(fn ($amount, $name) => [
                'name' => $name,
                'amount' => (float) $amount,
            ])
            ->values()
            ->toArray();

        $this->topExpenseCategories = collect($this->expenseByCategory)
            ->sortDesc()
            ->take(3)
            ->map(fn ($amount, $name) => [
                'name' => $name,
                'amount' => (float) $amount,
            ])
            ->values()
            ->toArray();

        $this->topIncomeCategory = $this->topIncomeCategories[0]['name'] ?? '';
        $this->topIncomeAmount = $this->topIncomeCategories[0]['amount'] ?? 0;
        $this->topExpenseCategory = $this->topExpenseCategories[0]['name'] ?? '';
        $this->topExpenseAmount = $this->topExpenseCategories[0]['amount'] ?? 0;
    }

    protected function buildWeeklyChart(): void
    {
        $start = now()->copy()->subDays(6)->startOfDay();
        $end = now()->copy()->endOfDay();

        $transactions = $this->schoolTransactionsQuery()
            ->whereBetween('transaction_date', [$start, $end])
            ->get(['transaction_date', 'type', 'amount']);

        $labels = [];
        $income = [];
        $expenses = [];

        $days = ['Dom', 'Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab'];

        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);
            $dateKey = $date->format('Y-m-d');

            $labels[] = $days[$date->dayOfWeek].' '.$date->format('d');

            $dayTransactions = $transactions->filter(
                fn ($transaction) => Carbon::parse($transaction->transaction_date)->format('Y-m-d') === $dateKey
            );

            $income[] = round((float) $dayTransactions->where('type', 'income')->sum('amount'), 2);
            $expenses[] = round((float) $dayTransactions->where('type', 'expense')->sum('amount'), 2);
        }

        $this->weeklyChart = compact('labels', 'income', 'expenses');
    }

    protected function buildMonthlyChart(): void
    {
        $transactions = $this->schoolTransactionsQuery()
            ->whereBetween('transaction_date', [
                now()->startOfYear(),
                now()->endOfYear(),
            ])
            ->get(['transaction_date', 'type', 'amount']);

        $labels = ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'];
        $income = [];
        $expenses = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthTransactions = $transactions->filter(
                fn ($transaction) => Carbon::parse($transaction->transaction_date)->month === $month
            );

            $income[] = round((float) $monthTransactions->where('type', 'income')->sum('amount'), 2);
            $expenses[] = round((float) $monthTransactions->where('type', 'expense')->sum('amount'), 2);
        }

        $this->monthlyChart = compact('labels', 'income', 'expenses');
    }

    protected function buildYearlyChart(): void
    {
        $firstYear = now()->year - 4;

        $transactions = $this->schoolTransactionsQuery()
            ->where('transaction_date', '>=', Carbon::create($firstYear, 1, 1)->startOfDay())
            ->get(['transaction_date', 'type', 'amount']);

        $labels = [];
        $income = [];
        $expenses = [];

        for ($year = $firstYear; $year <= now()->year; $year++) {
            $labels[] = (string) $year;

            $yearTransactions = $transactions->filter(
                fn ($transaction) => Carbon::parse($transaction->transaction_date)->year === $year
            );

            $income[] = round((float) $yearTransactions->where('type', 'income')->sum('amount'), 2);
            $expenses[] = round((float) $yearTransactions->where('type', 'expense')->sum('amount'), 2);
        }

        $this->yearlyChart = compact('labels', 'income', 'expenses');
    }

    protected function loadLatestTransactions(): void
    {
        $this->latestTransactions = $this->schoolTransactionsQuery()
            ->with('category')
            ->latest('transaction_date')
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(fn (FinancialTransaction $transaction) => [
                'id' => $transaction->id,
                'type' => $transaction->type,
                'amount' => (float) $transaction->amount,
                'date' => Carbon::parse($transaction->transaction_date)->format('d/m/Y'),
                'category' => $transaction->category?->name ?? 'Senza categoria',
                'description' => $transaction->description,
            ])
            ->toArray();
    }

    /**
     * Studenti con un saldo ancora da versare.
     * La quota dovuta è in students.total_fee, i versamenti in student_payments.
     * "paid" è l'alias di withSum: non chiamarlo "paid_amount", altrimenti
     * l'accessor del modello avrebbe la precedenza e rifarebbe una query per studente.
     */
    protected function loadUnpaidUsers(): void
    {
        $students = Student::query()
            ->withSum('payments as paid', 'amount')
            ->where('total_fee', '>', 0)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->filter(fn (Student $s) => (float) $s->total_fee > (float) ($s->paid ?? 0))
            ->values();

        $this->unpaidUsersCount = $students->count();

        $this->unpaidUsers = $students
            ->map(fn (Student $s) => [
                'id' => $s->id,
                'name' => $s->last_name.' '.$s->first_name,
                'email' => $s->email ?: 'Nessuna email',
                'remaining' => (float) $s->total_fee - (float) ($s->paid ?? 0),
            ])
            ->toArray();
    }
}
