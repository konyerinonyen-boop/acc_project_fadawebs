<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Schema;

class PaymentStatusChart extends ChartWidget
{
    protected ?string $heading = 'Payment status';

    protected ?string $description = 'A snapshot of payments by review status.';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 4;

    public static function canView(): bool
    {
        return (auth()->user()?->hasRole('super_admin') ?? false)
            && Schema::hasTable('payments');
    }

    protected function getData(): array
    {
        $payments = Payment::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'labels' => ['Approved', 'Pending', 'Suspended'],
            'datasets' => [
                [
                    'data' => [
                        (int) ($payments['approved'] ?? 0),
                        (int) ($payments['pending'] ?? 0),
                        (int) ($payments['suspended'] ?? 0),
                    ],
                    'backgroundColor' => ['#0f9f85', '#e6a23c', '#e56a67'],
                    'borderColor' => ['#ffffff', '#ffffff', '#ffffff'],
                    'borderWidth' => 3,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
