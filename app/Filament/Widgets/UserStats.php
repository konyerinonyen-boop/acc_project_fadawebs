<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\User;
use App\Models\Zone;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Members', User::role('member')->count())->description('Approved member accounts')->descriptionIcon('heroicon-m-user-group')->color('primary'),
            Stat::make('New members', User::role('member')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count())->description('Joined this month')->descriptionIcon('heroicon-m-user-plus')->color('info'),
            Stat::make('Payments to review', Payment::where('status', 'pending')->count())->description('Awaiting finance review')->descriptionIcon('heroicon-m-clock')->color('warning'),
            Stat::make('Zones', Zone::count())->description('Active communities')->descriptionIcon('heroicon-m-map')->color('gray'),
        ];
    }
}
