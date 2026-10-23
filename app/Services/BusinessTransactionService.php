<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\LedgerAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BusinessTransactionService
{
    public function createExpense(array $data): JournalEntry
    {
        return DB::transaction(function () use ($data): JournalEntry {
            $amount = (float) $data['amount'];
            $description = trim($data['description']);
            $transactionDate = $data['transaction_date'];
            $paymentMethod = $data['payment_method'];
            $paymentStatus = $data['payment_status'];

            if ($amount <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'The expense amount must be greater than zero.',
                ]);
            }

            if (! in_array($paymentStatus, ['paid', 'unpaid'], true)) {
                throw ValidationException::withMessages([
                    'payment_status' => 'Select either paid or unpaid.',
                ]);
            }

            $expenseAccount = $this->getActiveAccount('5000');

            // Paid expense: debit expense, credit cash/bank.
            // Unpaid expense: debit expense, credit accounts payable.
            $creditAccount = $paymentStatus === 'paid'
                ? $this->getActiveAccount('1000')
                : $this->getActiveAccount('2000');

            $entry = JournalEntry::create([
                'entry_date' => $transactionDate,
                'reference' => 'JRN-' . now()->format('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(3)),
                'description' => $description,
                'created_by' => auth()->id(),
            ]);

            $entry->lines()->createMany([
                [
                    'ledger_account_id' => $expenseAccount->id,
                    'debit' => $amount,
                    'credit' => 0,
                    'memo' => $description,
                ],
                [
                    'ledger_account_id' => $creditAccount->id,
                    'debit' => 0,
                    'credit' => $amount,
                    'memo' => $paymentStatus === 'paid'
                        ? 'Expense paid by ' . $paymentMethod
                        : 'Unpaid expense - accounts payable',
                ],
            ]);

            // Validate and post using the existing accounting service.
            app(AccountingService::class)->post($entry);

            return $entry->fresh(['lines.account', 'creator']);
        });
    }

    private function getActiveAccount(string $code): LedgerAccount
    {
        $account = LedgerAccount::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->first();

        if (! $account) {
            throw ValidationException::withMessages([
                'amount' => "Required ledger account {$code} is missing or inactive. Check your chart of accounts.",
            ]);
        }

        return $account;
    }
}
