<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Schema;

class BankAccountController extends Controller
{
    public function accounts()
    {
        if (! Schema::hasTable('bank_accounts')) {
            return response()->json([
                'status' => true,
                'data' => [
                    'membership_fee_account' => null,
                    'donation_account' => null,
                ],
            ]);
        }

        $membershipFeeAccount = BankAccount::where('type', 'membership_fee')->first();
        $donationAccount = BankAccount::where('type', 'donation')->first();

        return response()->json([
            'status' => true,
            'data' => [
                'membership_fee_account' => $membershipFeeAccount,
                'donation_account' => $donationAccount,
            ],
        ]);
    }
}
