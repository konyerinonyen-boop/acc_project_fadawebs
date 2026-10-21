<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\LedgerAccount;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountingService
{
    public function postApprovedPayment(Payment $payment): ?JournalEntry
    {
        if (
            $payment->status !== 'approved'
            || ! in_array($payment->description, ['membership_fee', 'donation'], true)
            || (float) $payment->amount <= 0
        ) {
            return null;
        }

        return DB::transaction(function () use ($payment): JournalEntry {
            $existing = JournalEntry::query()
                ->where('source_type', $payment->getMorphClass())
                ->where('source_id', $payment->id)
                ->first();
            if ($existing) {
                return $existing;
            }

            $cash = LedgerAccount::firstOrCreate(
                ['code' => '1000'],
                ['name' => 'Cash and bank', 'type' => 'asset', 'description' => 'Funds received and held by the organisation.', 'is_active' => true],
            );
            $incomeCode = $payment->description === 'donation' ? '4100' : '4000';
            $incomeName = $payment->description === 'donation' ? 'Donations income' : 'Membership dues income';
            $income = LedgerAccount::firstOrCreate(
                ['code' => $incomeCode],
                ['name' => $incomeName, 'type' => 'income', 'is_active' => true],
            );

            $entry = JournalEntry::create([
                'entry_date' => $payment->updated_at?->toDateString() ?? now()->toDateString(),
                'reference' => 'PAY-' . $payment->id,
                'description' => ucfirst(str_replace('_', ' ', $payment->description ?: 'member payment')) . ' · ' . ($payment->reference ?: 'Payment #' . $payment->id),
                'source_type' => $payment->getMorphClass(),
                'source_id' => $payment->id,
                'posted_at' => now(),
                'created_by' => auth()->id(),
            ]);

            $entry->lines()->createMany([
                ['ledger_account_id' => $cash->id, 'debit' => $payment->amount, 'credit' => 0, 'memo' => 'Funds received'],
                ['ledger_account_id' => $income->id, 'debit' => 0, 'credit' => $payment->amount, 'memo' => 'Recognise income'],
            ]);

            return $entry;
        });
    }

    public function post(JournalEntry $entry): void
    {
        $entry->load('lines');
        $debits = round((float) $entry->lines->sum('debit'), 2);
        $credits = round((float) $entry->lines->sum('credit'), 2);

        if ($entry->lines->count() < 2 || $debits <= 0 || abs($debits - $credits) > 0.009) {
            throw ValidationException::withMessages([
                'data.lines' => 'A journal entry needs at least two lines and equal, non-zero debit and credit totals.',
            ]);
        }

        if ($entry->lines->contains(fn ($line) => ((float) $line->debit > 0) === ((float) $line->credit > 0))) {
            throw ValidationException::withMessages([
                'data.lines' => 'Each journal line must contain either a debit or a credit, not both or neither.',
            ]);
        }

        $entry->update(['posted_at' => now()]);
    }
}
