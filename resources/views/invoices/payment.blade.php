<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #222; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 2px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { padding: 8px 10px; border-bottom: 1px solid #ddd; text-align: left; }
        .label { color: #666; width: 40%; }
        .total-row td { font-weight: bold; font-size: 16px; border-top: 2px solid #333; }
        .status { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 12px; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-suspended { background: #f8d7da; color: #721c24; }
        .footer { margin-top: 40px; font-size: 12px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $invoiceTitle }}</h1>
        <p>Reference: {{ $payment->reference }}</p>
    </div>

    <table>
        <tr>
            <td class="label">Member Name</td>
            <td>{{ $payment->user->name }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td>{{ $payment->user->email }}</td>
        </tr>
        <tr>
            <td class="label">Payment Type</td>
            <td>{{ $invoiceTitle }}</td>
        </tr>
        <tr>
            <td class="label">Payment Method</td>
            <td>{{ ucfirst($payment->payment_method ?? '-') }}</td>
        </tr>
        <tr>
            <td class="label">Date</td>
            <td>{{ $payment->created_at->format('F j, Y') }}</td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td>
                <span class="status status-{{ $payment->status }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </td>
        </tr>
        <tr class="total-row">
            <td>Amount</td>
            <td>₦{{ number_format((float) $payment->amount, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>This document was generated automatically and serves as a record of the transaction above.</p>
    </div>
</body>
</html>
