<?php

namespace App\Filament\Resources\BusinessTransactions\Pages;

use App\Filament\Resources\BusinessTransactions\BusinessTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBusinessTransaction extends EditRecord
{
    protected static string $resource = BusinessTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
