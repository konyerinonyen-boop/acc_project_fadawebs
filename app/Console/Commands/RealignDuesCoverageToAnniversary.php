<?php

namespace App\Console\Commands;

use App\Models\DuesPayment;
use App\Models\User;
use Illuminate\Console\Command;

class RealignDuesCoverageToAnniversary extends Command
{
    protected $signature = 'dues:realign-anniversary
        {--dry-run : Show what would change without writing anything}';

    protected $description = 'Realign existing DuesPayment coverage_start/coverage_end rows from calendar-month anchoring (old logic) to join-anniversary anchoring (new logic), per user.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        // Only users who actually have DuesPayment rows are affected.
        $userIds = DuesPayment::query()->distinct()->pluck('user_id');

        if ($userIds->isEmpty()) {
            $this->info('No DuesPayment rows found. Nothing to do.');
            return self::SUCCESS;
        }

        $this->info("Checking {$userIds->count()} user(s) with existing dues coverage.");

        $changed = 0;
        $alreadyAligned = 0;
        $skippedNoUser = 0;

        foreach ($userIds as $userId) {
            $user = User::find($userId);

            if (!$user) {
                $this->warn("  Skipping user_id={$userId}: user not found.");
                $skippedNoUser++;
                continue;
            }

            $anchorDay = $user->membership_start_date
                ? \Carbon\Carbon::parse($user->membership_start_date)->day
                : \Carbon\Carbon::parse($user->created_at)->day;

            $rows = DuesPayment::where('user_id', $userId)
                ->whereNotNull('coverage_start')
                ->whereNotNull('coverage_end')
                ->orderBy('coverage_start')
                ->get();

            if ($rows->isEmpty()) {
                continue;
            }

            // Walk this user's rows in order, re-deriving each one's months-covered
            // span from its OLD (month-truncated) coverage_start/coverage_end, then
            // rewriting both dates anchored to the join day, chained contiguously.
            $cursor = $user->membership_start_date
                ? \Carbon\Carbon::parse($user->membership_start_date)
                : \Carbon\Carbon::parse($user->created_at);

            foreach ($rows as $row) {
                $rowStart = \Carbon\Carbon::parse($row->coverage_start);
                $rowEnd = \Carbon\Carbon::parse($row->coverage_end);

                // Already aligned (e.g. previously fixed manually, like user #27) — skip.
                if ($rowStart->day === $anchorDay) {
                    $alreadyAligned++;
                    $cursor = $rowStart->copy()->addMonths($row->months_paid ?: 1);
                    continue;
                }

                $monthsInRow = $row->months_paid ?: 1;

                $newStart = $cursor->copy();
                $newEnd = $cursor->copy()->addMonths(max(0, $monthsInRow - 1));

                $this->line(sprintf(
                    '  User #%d (%s): DuesPayment #%d  %s..%s  ->  %s..%s',
                    $userId,
                    $user->email,
                    $row->id,
                    $rowStart->format('Y-m-d'),
                    $rowEnd->format('Y-m-d'),
                    $newStart->format('Y-m-d'),
                    $newEnd->format('Y-m-d'),
                ));

                if (!$dryRun) {
                    $row->update([
                        'coverage_start' => $newStart,
                        'coverage_end' => $newEnd,
                    ]);
                }

                $cursor = $cursor->copy()->addMonths($monthsInRow);
                $changed++;
            }
        }

        if ($dryRun) {
            $this->info("Dry run complete. Would realign {$changed} row(s); {$alreadyAligned} already aligned; {$skippedNoUser} skipped (no user).");
        } else {
            $this->info("Done. Realigned {$changed} row(s); {$alreadyAligned} already aligned; {$skippedNoUser} skipped (no user).");
        }

        return self::SUCCESS;
    }
}
