<?php

namespace App\Filament\Resources\AccountLedger\Widgets;

use App\Models\JournalEntryLine;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class AccountLedgerStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $types = [
            'asset' => 'Total Assets',
            'liability' => 'Total Liabilities',
            'equity' => 'Total Equity',
            'income' => 'Total Income',
            'expense' => 'Total Expenses',
        ];

        $stats = [];

        foreach ($types as $type => $label) {
            $totals = JournalEntryLine::query()
                ->whereHas('entry', function (Builder $query) {
                    $query->whereNotNull('posted_at');
                })
                ->whereHas('account', function (Builder $query) use ($type) {
                    $query->where('type', $type);
                })
                ->selectRaw('COALESCE(SUM(debit), 0) as total_debit')
                ->selectRaw('COALESCE(SUM(credit), 0) as total_credit')
                ->first();

            $debit = (float) $totals->total_debit;
            $credit = (float) $totals->total_credit;

            // Calculate the balance according to the account type.
            $balance = in_array($type, ['asset', 'expense'], true)
                ? $debit - $credit
                : $credit - $debit;

            $stats[] = Stat::make(
                $label,
                '₦' . number_format($balance, 2)
            );
        }

        return $stats;
    }

    protected function getColumns(): int|array
    {
        return 3;
    }
}