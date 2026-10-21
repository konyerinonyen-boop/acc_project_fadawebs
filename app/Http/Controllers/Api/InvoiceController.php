<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    public function show(int $id): Response
    {
        $payment = Payment::with('user')->findOrFail($id);

        // Make sure a user can only download their own invoice
        // (remove this check if admins should be able to fetch any payment's invoice)
        if ($payment->user_id !== Auth::id() && !Auth::user()->hasRole('super_admin')) {
            abort(403, 'You are not authorized to view this invoice.');
        }

        $invoiceTitle = match ($payment->description) {
            'membership_fee' => 'Membership Fee Invoice',
            'donation' => 'Donation Invoice',
            default => 'Payment Invoice',
        };

        $pdf = Pdf::loadView('invoices.payment', [
            'payment' => $payment,
            'invoiceTitle' => $invoiceTitle,
        ]);

        $filename = strtolower(str_replace(' ', '-', $invoiceTitle)) . "-{$payment->reference}.pdf";

        return $pdf->download($filename);
    }
}
