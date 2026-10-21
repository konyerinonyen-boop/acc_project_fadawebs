<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentHistoryController extends Controller
{
    public function duesPayments(Request $request)
    {
        return $this->paginatedHistory($request, 'membership_fee');
    }

    public function donationPayments(Request $request)
    {
        return $this->paginatedHistory($request, 'donation');
    }

    private function paginatedHistory(Request $request, string $description)
    {
        $user = Auth::user();
        $perPage = (int) $request->query('per_page', 10);

        $payments = Payment::where('user_id', $user->id)
            ->where('description', $description)
            ->where('status', 'approved')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $payments->getCollection()->transform(fn ($payment) => [
            'id' => $payment->id,
            'amount' => $payment->amount,
            'status' => $payment->status,
            'reference' => $payment->reference,
            'payment_method' => $payment->payment_method,
            'paid_on' => $payment->created_at->format('Y-m-d'),
        ]);

        return response()->json([
            'status' => true,
            'data' => $payments->items(),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ],
        ]);
    }
}
