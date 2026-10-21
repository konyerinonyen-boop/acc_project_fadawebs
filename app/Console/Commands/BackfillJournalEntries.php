<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\AccountingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class BackfillJournalEntries extends Command
{
    protected $signature = 'accounting:backfill-payments';

    protected $description = 'Post approved payments that do not yet have a journal entry.';

    public function handle(AccountingService $accounting): int
    {
        if (! Schema::hasTable('journal_entries')) {
            $this->error('Run database migrations before backfilling the accounting ledger.');

            return self::FAILURE;
        }

        $processed = 0;

        Payment::query()
            ->where('status', 'approved')
            ->orderBy('id')
            ->each(function (Payment $payment) use ($accounting, &$processed): void {
                $accounting->postApprovedPayment($payment);
                $processed++;
            });

        $this->info("Processed {$processed} approved payment(s).");

        return self::SUCCESS;
    }
}
