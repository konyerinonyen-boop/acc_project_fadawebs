<?php

namespace App\Filament\Resources\DuesPayments\Pages;

use App\Filament\Resources\DuesPayments\DuesPaymentResource;
use App\Models\Payment;
use App\Models\DuesPlan;
use Filament\Resources\Pages\CreateRecord;

class CreateDuesPayment extends CreateRecord
{
    protected static string $resource = DuesPaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Get plan
        $plan = DuesPlan::find($data['dues_plan_id']);

        // Calculate total amount
        $amount = $plan->monthly_amount * (int) $data['months_paid'];

        // Ensure coverage_end is correct (safety check)
        $data['coverage_end'] = \Carbon\Carbon::parse($data['coverage_start'])
            ->addMonths((int) $data['months_paid']);

        /**
         * 1. CREATE PAYMENT FIRST
         */
      $payment = Payment::create([
        'user_id' => $data['user_id'],
        'amount' => $amount,
        'reference' => 'PAY-' . strtoupper(uniqid()),
        'status' => $data['status'] ?? 'approved',
        'payment_method' => $data['payment_method'] ?? '',
    ]);


        /**
         * 2. ATTACH PAYMENT ID TO DUES RECORD
         */
        $data['payment_id'] = $payment->id;

        return $data;
    }
}
