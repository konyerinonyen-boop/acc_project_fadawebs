<?php

namespace App\Filament\Resources\JournalEntries\Pages;

use App\Filament\Resources\JournalEntries\JournalEntryResource;
use App\Models\JournalEntry;
use App\Models\LedgerAccount;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateJournalEntry extends CreateRecord
{
    protected static string $resource = JournalEntryResource::class;

    /**
     * Temporary debit and credit lines generated from the simple transaction form.
     */
    protected array $generatedLines = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        $type = $data['transaction_type'] ?? 'expense';

        // Manual adjustments use the journal-lines repeater instead.
        if ($type === 'adjustment') {
            unset(
                $data['transaction_type'],
                $data['amount'],
                $data['payment_status'],
                $data['account_id'],
                $data['cash_account_id'],
            );

            return $data;
        }

        $amount = round((float) ($data['amount'] ?? 0), 2);

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Enter an amount greater than zero.',
            ]);
        }

        $paymentStatus = $data['payment_status'] ?? 'paid';

        $mainAccount = $this->getActiveAccount(
            $data['account_id'] ?? null,
            'Main account',
        );

        $otherAccount = $this->getActiveAccount(
            $data['cash_account_id'] ?? null,
            'Other account',
        );

        $expectedTypes = match ($type) {
            'expense' => [
                'expense',
                $paymentStatus === 'unpaid' ? 'liability' : 'asset',
            ],
            'asset_purchase' => [
                'asset',
                $paymentStatus === 'unpaid' ? 'liability' : 'asset',
            ],
            'income' => ['income', 'asset'],
            'loan_received' => ['liability', 'asset'],
            'equity_contribution' => ['equity', 'asset'],
            'liability_payment' => ['liability', 'asset'],
            'transfer' => ['asset', 'asset'],
            default => throw ValidationException::withMessages([
                'transaction_type' => 'Select a valid transaction type.',
            ]),
        };

        if (
            $mainAccount->type !== $expectedTypes[0]
            || $otherAccount->type !== $expectedTypes[1]
        ) {
            throw ValidationException::withMessages([
                'account_id' => 'The selected accounts do not match this transaction type. Please check both accounts.',
            ]);
        }

        // Normally: debit the main account and credit the other account.
        $debitAccount = $mainAccount;
        $creditAccount = $otherAccount;

        // Income, loans received, and contributions increase the other asset account.
        if (in_array($type, [
            'income',
            'loan_received',
            'equity_contribution',
        ], true)) {
            $debitAccount = $otherAccount;
            $creditAccount = $mainAccount;
        }

        $this->generatedLines = [
            [
                'ledger_account_id' => $debitAccount->id,
                'debit' => $amount,
                'credit' => 0,
                'memo' => $data['description'],
            ],
            [
                'ledger_account_id' => $creditAccount->id,
                'debit' => 0,
                'credit' => $amount,
                'memo' => $data['description'],
            ],
        ];

        // These are form-only fields, not journal_entries table columns.
        unset(
            $data['transaction_type'],
            $data['amount'],
            $data['payment_status'],
            $data['account_id'],
            $data['cash_account_id'],
            $data['lines'],
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->generatedLines !== []) {
            $this->record->lines()->createMany($this->generatedLines);
        }
    }

    private function getActiveAccount(
        mixed $accountId,
        string $fieldLabel,
    ): LedgerAccount {
        $account = LedgerAccount::query()
            ->whereKey($accountId)
            ->where('is_active', true)
            ->first();

        if (! $account) {
            throw ValidationException::withMessages([
                'account_id' => "Select a valid active {$fieldLabel}.",
            ]);
        }

        return $account;
    }
}