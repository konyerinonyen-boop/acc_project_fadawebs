<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Zone;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStats extends StatsOverviewWidget
{
   protected function getStats(): array
{
    return [
        Stat::make('Total Users', User::count()),

        Stat::make(
            'Members',
            User::whereNotNull('membership_id')->count()
        ),

        Stat::make(
            'New Users This Month',
            User::whereMonth('created_at', now()->month)->count()
        ),

         Stat::make(
            'Number of Zones',
            Zone::count()
        ),
    ];
}
}
