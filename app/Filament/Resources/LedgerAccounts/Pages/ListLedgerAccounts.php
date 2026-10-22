<?php

namespace App\Filament\Resources\LedgerAccounts\Pages;

use App\Filament\Resources\AccountLedger\AccountLedgerResource;
use App\Filament\Resources\AccountLedger\Widgets\AccountLedgerStats;
use App\Filament\Resources\LedgerAccounts\LedgerAccountResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use Override;

class ListLedgerAccounts extends ListRecords
{
    protected static string $resource = LedgerAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewAccountLedger')
                ->label('View account ledger')
                ->icon('heroicon-o-book-open')
                ->url(AccountLedgerResource::getUrl('index')),

            CreateAction::make()
                ->label('New account'),
        ];
    }

   protected function getHeaderWidgets(): array
{
    return [
        AccountLedgerStats::class,
    ];
}
}
