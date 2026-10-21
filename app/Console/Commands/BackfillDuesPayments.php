<?php

namespace App\Console\Commands;

use App\Models\DuesPayment;
use App\Models\DuesPlan;
use App\Models\Payment;
use Illuminate\Console\Command;

class BackfillDuesPayments extends Command
{
    protected $signature = 'dues:backfill
        {--dry-run : Show what would be created without writing anything}';

    protected $description = 'Create missing DuesPayment rows for approved membership_fee payments that never got one (e.g. approved outside the EditPayment Filament page).';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $orphanPayments = Payment::query()
            ->where('status', 'approved')
            ->where('description', 'membership_fee')
            ->whereDoesntHave('dues')
            ->orderBy('user_id')
            ->orderBy('created_at')
            ->get();

        if ($orphanPayments->isEmpty()) {
            $this->info('No orphaned membership_fee payments found. Nothing to do.');
            return self::SUCCESS;
        }

        $this->info("Found {$orphanPayments->count()} approved membership_fee payment(s) with no DuesPayment row.");

        $created = 0;
        $skipped = 0;

        // Process per-user so coverage_start correctly chains off the months
        // already covered by earlier payments in this same run, not just
        // whatever was in the DB before the command started.
        foreach ($orphanPayments->groupBy('user_id') as $userId => $userPayments) {
            $user = $userPayments->first()->user;

            if (!$user) {
                $this->warn("Skipping payment(s) for missing user_id={$userId}.");
                $skipped += $userPayments->count();
                continue;
            }

            foreach ($userPayments as $payment) {
                $monthlyAmount = DuesPlan::where('is_active', true)->value('monthly_amount')
                    ?? DuesPlan::first()?->monthly_amount
                    ?? 0;
                $monthsCovered = $monthlyAmount > 0
                    ? (int) floor($payment->amount / $monthlyAmount)
                    : 0;
                $monthsCovered = max($monthsCovered, $payment->amount > 0 ? 1 : 0);

                if ($monthsCovered <= 0) {
                    $this->warn("  Payment #{$payment->id} (user #{$userId}): amount too low to cover any month, skipping.");
                    $skipped++;
                    continue;
                }

                // nextDueDate() re-evaluates monthsPaid() each call, so after we
                // create a DuesPayment below, the next iteration for this same
                // user picks up the new coverage automatically.
                $coverageStart = $user->nextDueDate();
                $coverageEnd = $coverageStart->copy()->addMonths($monthsCovered - 1);

                $this->line(sprintf(
                    '  Payment #%d (user #%d, %s, ₦%s): would cover %d month(s), %s → %s',
                    $payment->id,
                    $userId,
                    $user->email,
                    number_format((float) $payment->amount, 2),
                    $monthsCovered,
                    $coverageStart->format('Y-m'),
                    $coverageEnd->format('Y-m'),
                ));

                if (!$dryRun) {
                    $duesPlanId = $payment->dues_plan_id
                        ?? \App\Models\DuesPlan::where('is_active', true)->value('id')
                        ?? \App\Models\DuesPlan::first()?->id;

                    DuesPayment::create([
                        'payment_id' => $payment->id,
                        'user_id' => $user->id,
                        'months_paid' => $monthsCovered,
                        'coverage_start' => $coverageStart,
                        'coverage_end' => $coverageEnd,
                        'dues_plan_id' => $duesPlanId,
                    ]);

                    // Refresh so nextDueDate() inside this loop sees the new row
                    // on the next payment for the same user.
                    $user->unsetRelation('duesPayments');
                }

                $created++;
            }
        }

        if ($dryRun) {
            $this->info("Dry run complete. Would create {$created} DuesPayment row(s), {$skipped} skipped.");
        } else {
            $this->info("Done. Created {$created} DuesPayment row(s), {$skipped} skipped.");
        }

        return self::SUCCESS;
    }
}
