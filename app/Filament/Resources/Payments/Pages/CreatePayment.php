<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // PAYMENT reference (always generated)
        $data['reference'] = 'PAY-' . strtoupper(Str::random(10));

        // link to admin or selected user (if you have user_id in form)
        // $data['user_id'] = auth()->id();

        return $data;
    }
}
