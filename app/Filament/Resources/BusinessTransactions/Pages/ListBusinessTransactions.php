<?php

namespace App\Filament\Resources\BusinessTransactions\Pages;

use App\Filament\Resources\BusinessTransactions\BusinessTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBusinessTransactions extends ListRecords
{
    protected static string $resource = BusinessTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Record a transaction')
                ->icon('heroicon-o-plus'),
        ];
    }
}
