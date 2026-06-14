<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DuesPlan;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{

public function manualDonation(Request $request)
{

    $validatedData = $request->validate([
        'payment_type' => 'required',
        'amount'              => 'required|numeric|min:1',
        'payment_method'      => 'required|string|max:255',
        'payment_proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);


    $validatedData['user_id'] = auth('sanctum')->id();


    $validatedData['reference'] = 'REF-' . time() . rand(1000, 9999);
    $validatedData['status'] = 'pending';
    $validatedData['description'] = 'donation';


    if ($request->hasFile('payment_proof_image')) {
        $image = $request->file('payment_proof_image');
        $imageName = time() . '_proof.jpg';

        if (!file_exists(public_path('images/payments'))) {
            mkdir(public_path('images/payments'), 0777, true);
        }

        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        $manager->read($image)->scale(width: 800)->toJpeg(80)->save(public_path('images/payments/' . $imageName));
        $validatedData['payment_proof_image'] = 'images/payments/' . $imageName;
    }


    $payment =Payment::create($validatedData);

    return response()->json([
        'status' => 'success',
        'message' => 'Payment recorded successfully!',
        'data' => $payment
    ], 201);
}


public function donationSummary()
{
    $userId = Auth::id();


    $donations = Payment::where('user_id', $userId)
        ->where('description', 'donation')
        ->get();


    if ($donations->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'You have not made any donations yet.',
            'total_sum' => 0.0,
            'data' => []
        ], 404);
    }


    $totalDonationAmount = (float) Payment::where('user_id', $userId)
        ->where('description', 'donation')
        ->sum('amount');


    return response()->json([
        'status' => true,
        'message' => 'Donation summary retrieved successfully.',
        'total_sum' => $totalDonationAmount,
        'data' => $donations
    ], 200);
}

public function memberFeeSummary()
{
    $userId = Auth::id();


    $fee = Payment::where('user_id', $userId)
        ->where('description', 'membership_fee')
        ->get();


    if ($fee->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'You have not made any membership fee payments yet.',
            'total_sum' => 0,
            'data' => []
        ], 404);
    }


    $totalFeeAmount = Payment::where('user_id', $userId)
        ->where('description', 'membership_fee')
        ->sum('amount');


    return response()->json([
        'status' => true,
        'message' => 'Membership fee summary retrieved successfully.',
        'total_sum' => (float) $totalFeeAmount,
        'data' => $fee
    ], 200);
}


public function calculateImpactPoints()
{

    $userId = Auth::id();


    $totalDonations = (float) Payment::where('user_id', $userId)
        ->where('description', 'donation')
        ->where('status', 'approved')
        ->sum('amount');


    $totalFees = (float) Payment::where('user_id', $userId)
        ->where('description', 'membership_fee')
        ->where('status', 'approved')
        ->sum('amount');


   $donationUnits = $totalDonations / 1000;
    $feeUnits      = $totalFees / 1000;


    $donationPoints = $donationUnits * 10;
    $feePoints      = $feeUnits * 5;


    return (int) ($donationPoints + $feePoints);
}


public function pay(Request $request)
{
    $validated = $request->validate([
        'dues_plan_id'        => 'nullable|exists:dues_plans,id',
        'amount'              => 'required|numeric|min:1',
        'payment_method'      => 'required|string|max:255',
        'description'         => 'required|string|in:membership_fee,donation',
        'payment_proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $validated['user_id'] = auth('sanctum')->id();
    $validated['reference'] = 'REF-' . time() . rand(1000, 9999);
    $validated['status'] = 'pending';

    if ($request->hasFile('payment_proof_image')) {

        $directory = public_path('images/payments');

        if (! file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $image = $request->file('payment_proof_image');

        $imageName = time() . '_' . $image->getClientOriginalName();

        $image->move($directory, $imageName);

        $validated['payment_proof_image'] = 'images/payments/' . $imageName;
    }

    $payment = Payment::create($validated);

    return response()->json([
        'status' => 'success',
        'message' => 'Payment submitted successfully',
        'data' => $payment,
    ], 201);
}

public function paymentType()
{
    // Fetch only the 'description' and 'id' columns
    $payments = Payment::whereIn('description', ['donation', 'membership_fee'])
        ->select('id', 'description')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $payments
    ], 200);
}

public function plans()
{
    // Fetch all dues plans
    $plans = DuesPlan::all();

    return response()->json([
        'status' => 'success',
        'data' => $plans
    ], 200);
}

}
