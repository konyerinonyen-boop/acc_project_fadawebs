<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FinanceOverview extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return (auth()->user()?->hasRole('super_admin') ?? false)
            && Schema::hasTable('journal_entries')
            && Schema::hasTable('journal_entry_lines')
            && Schema::hasTable('ledger_accounts');
    }

    protected function getStats(): array
    {
        $posted = fn () => DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('ledger_accounts', 'ledger_accounts.id', '=', 'journal_entry_lines.ledger_account_id')
            ->whereNotNull('journal_entries.posted_at');

        $income = $posted()->where('ledger_accounts.type', 'income')->sum('journal_entry_lines.credit');
        $expenses = $posted()->where('ledger_accounts.type', 'expense')->sum('journal_entry_lines.debit');
        $monthIncome = $posted()->where('ledger_accounts.type', 'income')->whereMonth('journal_entries.entry_date', now()->month)->whereYear('journal_entries.entry_date', now()->year)->sum('journal_entry_lines.credit');
        $monthExpenses = $posted()->where('ledger_accounts.type', 'expense')->whereMonth('journal_entries.entry_date', now()->month)->whereYear('journal_entries.entry_date', now()->year)->sum('journal_entry_lines.debit');

        return [
            Stat::make('Income this month', '₦' . number_format((float) $monthIncome, 2))
                ->description('Posted income · all time ₦' . number_format((float) $income, 2))
                ->descriptionIcon('heroicon-m-arrow-trending-up')->color('success'),
            Stat::make('Expenses this month', '₦' . number_format((float) $monthExpenses, 2))
                ->description('Posted expenses · all time ₦' . number_format((float) $expenses, 2))
                ->descriptionIcon('heroicon-m-arrow-trending-down')->color('warning'),
            Stat::make('Net this month', '₦' . number_format((float) $monthIncome - (float) $monthExpenses, 2))
                ->description('Income less expenses for ' . now()->format('F Y'))
                ->descriptionIcon('heroicon-m-scale')->color('primary'),
            Stat::make('Draft entries', \App\Models\JournalEntry::whereNull('posted_at')->count())
                ->description('Waiting for review and posting')->descriptionIcon('heroicon-m-document-text')->color('gray'),
        ];
    }
}
