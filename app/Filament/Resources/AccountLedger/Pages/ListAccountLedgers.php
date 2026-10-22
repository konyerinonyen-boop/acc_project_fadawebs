<?php

namespace App\Filament\Resources\AccountLedger\Pages;

use App\Filament\Resources\AccountLedger\AccountLedgerResource;
use App\Models\JournalEntryLine;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAccountLedgerLines extends ListRecords
{
    protected static string $resource = AccountLedgerResource::class;

    /**
     * Count only lines belonging to posted journal entries.
     */
    private function postedLinesCount(?string $accountType = null): int
    {
        $query = JournalEntryLine::query()
            ->whereHas('entry', function (Builder $query) {
                $query->whereNotNull('posted_at');
            });

        if ($accountType !== null) {
            $query->whereHas('account', function (Builder $query) use ($accountType) {
                $query->where('type', $accountType);
            });
        }

        return $query->count();
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All lines')
                ->badge(fn () => $this->postedLinesCount()),

            'assets' => Tab::make('Assets')
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->whereHas(
                        'account',
                        fn (Builder $accountQuery) => $accountQuery->where('type', 'asset')
                    )
                )
                ->badge(fn () => $this->postedLinesCount('asset')),

            'liabilities' => Tab::make('Liabilities')
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->whereHas(
                        'account',
                        fn (Builder $accountQuery) => $accountQuery->where('type', 'liability')
                    )
                )
                ->badge(fn () => $this->postedLinesCount('liability')),

            'equity' => Tab::make('Equity')
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->whereHas(
                        'account',
                        fn (Builder $accountQuery) => $accountQuery->where('type', 'equity')
                    )
                )
                ->badge(fn () => $this->postedLinesCount('equity')),

            'income' => Tab::make('Income')
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->whereHas(
                        'account',
                        fn (Builder $accountQuery) => $accountQuery->where('type', 'income')
                    )
                )
                ->badge(fn () => $this->postedLinesCount('income')),

            'expenses' => Tab::make('Expenses')
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->whereHas(
                        'account',
                        fn (Builder $accountQuery) => $accountQuery->where('type', 'expense')
                    )
                )
                ->badge(fn () => $this->postedLinesCount('expense')),
        ];
    }
}
