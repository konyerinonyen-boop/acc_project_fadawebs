<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DuesPlan;
use App\Models\Event;
use App\Models\Payment;
use App\Models\ResourceMaterial;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Eager-load zone here so $user->zone below doesn't trigger an extra query
        $user = Auth::user()->load('zone');

        // Last approved membership payment (history/receipt only — never used for due calculations)
        $lastPayment = Payment::where('user_id', $user->id)
            ->where('description', 'membership_fee')
            ->where('status', 'approved')
            ->latest()
            ->first();

        // Total approved donations
        $totalDonations = (float) Payment::where('user_id', $user->id)
            ->where('description', 'donation')
            ->where('status', 'approved')
            ->sum('amount');

        // Total approved membership fees
        $totalMembershipFees = (float) Payment::where('user_id', $user->id)
            ->where('description', 'membership_fee')
            ->where('status', 'approved')
            ->sum('amount');

        // Impact points calculation
        $impactPoints =
            (sqrt($totalDonations / 1000) * 5) +
            (sqrt($totalMembershipFees / 1000) * 3);

        // Total upcoming events count
        $liveSessions = Event::whereDate('date', '>=', today())
            ->count();

        // Upcoming events
        $upcomingEvents = Event::whereDate('date', '>=', today())
            ->orderBy('date')
            ->take(3)
            ->get();

        // Latest resources
        $resources = ResourceMaterial::latest()
            ->take(5)
            ->get();

        // Membership dues — single source of truth for anything owed/due
        $monthlyAmount = DuesPlan::first()?->monthly_amount ?? 0;
        $monthsSinceJoin = $user->monthsSinceJoin();
        $monthsPaid = $user->monthsPaid();
        $monthsOwing = $user->monthsOwing();
        $dueAmount = $monthsOwing * $monthlyAmount;
        $nextDueDate = $user->nextDueDate();
        $isOwing = $monthsOwing > 0;

        // Dynamic wording for the frontend — one sentence, always accurate, no guesswork on the client side
        $formattedDue = number_format((float) $dueAmount, 2);
        $formattedMonthly = number_format((float) $monthlyAmount, 2);

        if ($isOwing) {
            $duesMessage = $monthsOwing === 1
                ? "You owe ₦{$formattedDue} for this month. Pay now to stay active."
                : "You owe ₦{$formattedDue} for {$monthsOwing} months. Pay now to clear your balance.";
        } else {
            // "Caught up" should read as coverage, not a looming deadline — the user
            // isn't late, the next billing cycle just hasn't started yet. Show the
            // month the next payment will cover, not a single calendar date that
            // can look like a due-by deadline (e.g. "due on July 1" reads like a
            // 1-day grace period when really the whole month of July is open).
            $duesMessage = $nextDueDate
                ? "You're all caught up. Your next payment of ₦{$formattedMonthly} covers "
                . $nextDueDate->format('F Y') . '.'
                : "You're all caught up. No dues plan is set yet.";
        }
        
         $NewMemberNotPaid = false;
        if ($user->membership_id==null && $user->status=='pending')
            {
                $NewMemberNotPaid = true;
            }

        return response()->json([
            'status' => true,
            'data' => [
                'account_status' => $user->status ?? 'pending',

                'last_payment' => [
                    'amount' => $lastPayment?->amount ?? 0,
                    'paid_on' => $lastPayment?->created_at?->format('Y-m-d'),
                ],

                'impact_points' => (int) $impactPoints,

                'total_donations' => $totalDonations,

                'total_membership_fees' => $totalMembershipFees,

                'dues' => [
                    'monthly_amount' => $monthlyAmount,
                    'months_since_join' => $monthsSinceJoin,
                    'months_paid' => $monthsPaid,
                    'months_owing' => $monthsOwing,
                    'due_amount' => $dueAmount,
                    'next_due_date' => $isOwing ? null : $nextDueDate?->format('Y-m-d'),
                    'next_due_amount' => $isOwing ? null : $monthlyAmount,
                    'is_owing' => $isOwing,
                    'message' => $duesMessage,
                ],

                'live_sessions' => $liveSessions,

                'upcoming_events' => $upcomingEvents,

                'notifications_count' => 0,

                'quick_resources' => $resources,

                // Everything about the user, merged in here instead of a separate user() endpoint
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'gender' => $user->gender,
                    'profile_image' => $user->profile_image,
                    'membership_id' => $user->membership_id,
                    'status' => $user->status,
                    'payment_status' => $user->payment_status,
                    'membership_start_date' => $user->membership_start_date?->format('Y-m-d'),
                    'zone' => $user->zone?->name,
                    'qr_code' => $user->qrCodeSvg(),
                    'qr_url' => $user->qrCodeUrl(),
                    'email_verified_at' => $user->email_verified_at,
                    'NewMemberNotPaid' => $NewMemberNotPaid

                ],
            ]
        ]);
    }
}