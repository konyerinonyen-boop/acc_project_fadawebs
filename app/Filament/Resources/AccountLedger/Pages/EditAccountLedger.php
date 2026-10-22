<?php

namespace App\Filament\Resources\AccountLedgers\Pages;

use App\Filament\Resources\AccountLedgers\AccountLedgerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAccountLedger extends EditRecord
{
    protected static string $resource = AccountLedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
