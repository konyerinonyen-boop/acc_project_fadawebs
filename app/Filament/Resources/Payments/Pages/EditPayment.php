<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
    protected function afterSave(): void
{
    $payment = $this->record;

    if ($payment->status === 'approved') {

        $user = $payment->user;

        if (!$user->membership_id) {
            $user->update([
                'status' => 'approved',
                'payment_status' => true,
                'membership_id' => 'MEM-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
            ]);
        }
    }

    if ($payment->status === 'suspended') {

        $payment->user->update([
            'status' => 'suspended',
            'payment_status' => false,
        ]);
    }
}
}
