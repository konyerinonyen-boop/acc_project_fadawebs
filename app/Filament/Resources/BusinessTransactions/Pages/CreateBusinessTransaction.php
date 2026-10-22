<?php

namespace App\Filament\Resources\BusinessTransactions\Pages;

use App\Filament\Resources\BusinessTransactions\BusinessTransactionResource;
use App\Services\BusinessTransactionService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBusinessTransaction extends CreateRecord
{
    protected static string $resource = BusinessTransactionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(BusinessTransactionService::class)
            ->createExpense($data);
    }
}
