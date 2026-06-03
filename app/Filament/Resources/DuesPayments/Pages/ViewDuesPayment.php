<?php

namespace App\Filament\Resources\DuesPayments\Pages;

use App\Filament\Resources\DuesPayments\DuesPaymentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDuesPayment extends ViewRecord
{
    protected static string $resource = DuesPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
