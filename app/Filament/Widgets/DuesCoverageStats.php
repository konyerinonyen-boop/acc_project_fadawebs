<?php

namespace App\Filament\Widgets;

use App\Models\DuesPayment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DuesCoverageStats extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Dues received', '₦' . number_format(
                (float) DuesPayment::query()
                    ->whereHas('payment', fn ($query) => $query->where('status', 'approved'))
                    ->with('payment:id,amount')
                    ->get()
                    ->sum(fn (DuesPayment $duesPayment) => (float) ($duesPayment->payment?->amount ?? 0)),
                2,
            ))->description('Approved payments with dues coverage')->color('success'),
            Stat::make('Coverage records', DuesPayment::count())->description('Recorded member periods')->color('primary'),
            Stat::make('Members due', \App\Models\User::role('member')->get()->filter(fn (\App\Models\User $member) => $member->monthsOwing() > 0)->count())->description('At least one unpaid period')->color('warning'),
        ];
    }
}
