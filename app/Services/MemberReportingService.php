<?php

namespace App\Services;

use App\Models\User;

class MemberReportingService
{
    public function getMemberStatus(User $user): array
    {
        $monthsOwing = $user->monthsOwing();

        $latestPayment = $user->duesPayments()->latest('created_at')->first();
        $monthlyFee = $latestPayment && $latestPayment->duesPlan
                      ? $latestPayment->duesPlan->monthly_amount
                      : 0;

        return [
            'full_name'           => $user->getNameAttribute(),
            'months_since_joined' => $user->monthsSinceJoin(),
            'total_months_paid'   => $user->monthsPaid(),
            'months_owing'        => $monthsOwing,
            'is_owing'            => $monthsOwing > 0,
            'total_due_amount'    => $user->monthsSinceJoin() * $monthlyFee,
            'total_owing_amount'  => $monthsOwing * $monthlyFee,
        ];
    }

    // ADD THIS METHOD TO FIX THE ERROR
    public function getZoneSummary(int $zoneId): array
    {
        $users = User::where('zone_id', $zoneId)->get();

        return [
            'total_members'        => $users->count(),
            'total_arrears_months' => $users->sum(fn($u) => $u->monthsOwing()),
            'compliance_rate'      => $users->count() > 0
                                      ? round(($users->filter(fn($u) => $u->monthsOwing() == 0)->count() / $users->count()) * 100, 2)
                                      : 0,
        ];
    }
}
