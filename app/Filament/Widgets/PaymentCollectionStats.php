<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentCollectionStats extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    protected function getStats(): array
    {
        $approved = Payment::where('status', 'approved');
        return [
            Stat::make('Approved collections', '₦' . number_format((float) (clone $approved)->sum('amount'), 2))->description('All approved payments')->color('success'),
            Stat::make('Collected this month', '₦' . number_format((float) (clone $approved)->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year)->sum('amount'), 2))->description(now()->format('F Y'))->color('primary'),
            Stat::make('Awaiting review', Payment::where('status', 'pending')->count())->description('Payments needing attention')->color('warning'),
        ];
    }
}
