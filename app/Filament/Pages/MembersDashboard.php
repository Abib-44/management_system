<?php

namespace App\Filament\Pages;

use App\Models\Member;
use App\Models\MembershipFee;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class MembersDashboard extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.members-dashboard';

    public function getTotalMembers(): int
    {
        return Member::count();
    }

    public function getNewMembersThisMonth(): int
    {
        return Member::whereBetween('registration_date', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ])->count();
    }

    public function getRenewalsNext30Days(): int
    {
        return Member::whereBetween('renewal_date', [
            today(),
            today()->copy()->addDays(30),
        ])->count();
    }

    public function getExpiredRenewals(): int
    {
        return Member::whereNotNull('renewal_date')
            ->whereDate('renewal_date', '<', today())
            ->count();
    }

    public function getTotalAnnualFees(): float
    {
        return (float) Member::sum('annual_fee');
    }

    public function getTotalFeesPaidThisYear(): float
    {
        return (float) MembershipFee::whereBetween('payment_date', [
            now()->startOfYear(),
            now()->endOfYear(),
        ])->sum('amount');
    }

    public function getMembersWithFeePaymentsThisYear(): int
    {
        return MembershipFee::whereBetween('payment_date', [
            now()->startOfYear(),
            now()->endOfYear(),
        ])
            ->distinct('member_id')
            ->count('member_id');
    }

    public function getMembersByStatus(): array
    {
        return Member::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->pluck('total', 'status')
            ->toArray();
    }
}
