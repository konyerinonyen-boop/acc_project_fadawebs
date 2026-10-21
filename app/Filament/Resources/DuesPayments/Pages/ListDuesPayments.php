<?php

namespace App\Filament\Resources\DuesPayments\Pages;

use App\Filament\Resources\DuesPayments\DuesPaymentResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\DuesCoverageStats;

class ListDuesPayments extends ListRecords
{
    protected static string $resource = DuesPaymentResource::class;

    protected function getHeaderWidgets(): array
    {
        return [DuesCoverageStats::class];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 3;
    }
}
