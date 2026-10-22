<?php

namespace App\Services;

use App\Models\BusinessTransaction;
use App\Models\JournalEntry;
use App\Models\LedgerAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BusinessTransactionService
{
    public function createExpense(array $data): BusinessTransaction
    {
        return DB::transaction(function () use ($data): BusinessTransaction {
            $transaction = BusinessTransaction::create([
                'transaction_type' => 'expense',
                'amount' => $data['amount'],
                'description' => trim($data['description']),
                'transaction_date' => $data['transaction_date'],
                'payment_method' => $data['payment_method'],
                'payment_status' => $data['payment_status'],
                'created_by' => auth()->id(),
            ]);

            $expenseAccount = $this->getActiveAccount('5000');

            // Paid expenses reduce cash/bank.
            // Unpaid expenses create an accounts-payable liability.
            $creditAccount = $data['payment_status'] === 'paid'
                ? $this->getActiveAccount('1000')
                : $this->getActiveAccount('2000');

            $entry = JournalEntry::create([
                'entry_date' => $transaction->transaction_date->toDateString(),
                'reference' => 'BT-' . $transaction->id,
                'description' => $transaction->description,
                'source_type' => $transaction->getMorphClass(),
                'source_id' => $transaction->id,
                'created_by' => auth()->id(),
            ]);

            $entry->lines()->createMany([
                [
                    'ledger_account_id' => $expenseAccount->id,
                    'debit' => $transaction->amount,
                    'credit' => 0,
                    'memo' => $transaction->description,
                ],
                [
                    'ledger_account_id' => $creditAccount->id,
                    'debit' => 0,
                    'credit' => $transaction->amount,
                    'memo' => $data['payment_status'] === 'paid'
                        ? 'Expense paid by ' . $data['payment_method']
                        : 'Expense not yet paid',
                ],
            ]);

            // Reuse the existing accounting validation.
            app(AccountingService::class)->post($entry);

            $transaction->update([
                'journal_entry_id' => $entry->id,
            ]);

            return $transaction->fresh(['journalEntry']);
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
                'amount' => "The required ledger account {$code} is missing or inactive. Check the chart of accounts.",
            ]);
        }

        return $account;
    }
}
