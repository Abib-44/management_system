<?php

namespace App\Filament\Pages;

use App\Models\ServiceAssignment;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class ServicesDashboard extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.services-dashboard';

    public function getTotal(): int
    {
        return ServiceAssignment::count();
    }

    public function getActive(): int
    {
        return ServiceAssignment::where('status', 'active')->count();
    }

    public function getDelivered(): int
    {
        return ServiceAssignment::where('status', 'delivered')->count();
    }

    public function getReturned(): int
    {
        return ServiceAssignment::where('status', 'returned')->count();
    }

    public function getLost(): int
    {
        return ServiceAssignment::where('status', 'lost')->count();
    }

    public function getCompleted(): int
    {
        return ServiceAssignment::where('status', 'completed')->count();
    }
}
