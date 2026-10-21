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
            DeleteAction::make()->visible(fn () => $this->record->status !== 'approved'),
        ];
    }

    protected function afterSave(): void
    {
        $payment = $this->record;
        $user = $payment->user;

        if ($payment->status === 'approved' && $payment->description === 'membership_fee' && $user) {
            if (!$user->membership_id) {
                $user->update([
                    'status' => 'approved',
                    'payment_status' => true,
                    'membership_start_date' => now(),
                    'membership_id' => 'MEM-' . rand(10000, 99999),
                ]);
            }

            $monthlyAmount = \App\Models\DuesPlan::where('is_active', true)->value('monthly_amount')
                ?? \App\Models\DuesPlan::first()?->monthly_amount
                ?? 0;
            $monthsCovered = $monthlyAmount > 0
                ? (int) floor($payment->amount / $monthlyAmount)
                : 0;
            $monthsCovered = max($monthsCovered, $payment->amount > 0 ? 1 : 0);

            // Only create a DuesPayment if this payment doesn't already have one
            // (afterSave can run more than once per record, e.g. on repeated edits/saves)
            $alreadyRecorded = \App\Models\DuesPayment::where('payment_id', $payment->id)->exists();

            if (! $alreadyRecorded && $monthsCovered > 0) {
                    // Start coverage where the user's existing paid-up period actually ends,
                    // not always from "today" — prevents overlapping coverage spans.
                    // No startOfMonth() here: coverage is anchored to the join day
                    // (e.g. June 29), matching User::monthsPaid()'s anniversary logic.
                $coverageStart = $user->nextDueDate();

                    // coverage_end is the START of the LAST covered month (inclusive),
                    // not the start of the month after. monthsPaid() computes
                    // diffInMonths(coverageStart, coverageEnd) + 1, so for a 1-month
                    // payment coverageEnd must equal coverageStart itself.
                $coverageEnd = $coverageStart->copy()->addMonths($monthsCovered - 1);

                    // dues_payments.dues_plan_id is NOT NULL, but payments aren't always
                    // created with a dues_plan_id set. Fall back to the active plan
                    // rather than letting this insert fail silently.
                $duesPlanId = $payment->dues_plan_id
                    ?? \App\Models\DuesPlan::where('is_active', true)->value('id')
                    ?? \App\Models\DuesPlan::first()?->id;

                \App\Models\DuesPayment::create([
                    'payment_id' => $payment->id,
                    'user_id' => $user->id,
                    'months_paid' => $monthsCovered,
                    'coverage_start' => $coverageStart,
                    'coverage_end' => $coverageEnd,
                    'dues_plan_id' => $duesPlanId,
                ]);
            }

            $user->syncRoles(['member']);
        }

        if ($payment->status === 'suspended' && $payment->description === 'membership_fee' && $user) {
            $user->update([
                'status' => 'suspended',
                'payment_status' => false,
            ]);
        }
    }
}
