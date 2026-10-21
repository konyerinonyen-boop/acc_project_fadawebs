<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Schema;

class PaymentTrendChart extends ChartWidget
{
    protected ?string $heading = 'Collection trend';

    protected ?string $description = 'Approved payment collections over the last six months.';

    protected int|string|array $columnSpan = 2;

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return (auth()->user()?->hasRole('super_admin') ?? false)
            && Schema::hasTable('payments');
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))
            ->map(fn (int $offset) => now()->startOfMonth()->subMonths($offset));

        return [
            'labels' => $months->map(fn ($month) => $month->format('M Y'))->all(),
            'datasets' => [
                [
                    'label' => 'Approved collections (₦)',
                    'data' => $months->map(fn ($month) => (float) Payment::query()
                        ->where('status', 'approved')
                        ->whereBetween('created_at', [
                            $month->copy()->startOfMonth(),
                            $month->copy()->endOfMonth(),
                        ])
                        ->sum('amount'))->all(),
                    'borderColor' => '#087f72',
                    'backgroundColor' => 'rgba(8, 127, 114, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
