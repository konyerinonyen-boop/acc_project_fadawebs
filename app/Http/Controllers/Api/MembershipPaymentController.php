<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\MembershipFee;
use Illuminate\Http\Request;
use App\Models\Payment;

class MembershipPaymentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'membership_fee_id'   => 'required|exists:membership_fees,id',
            'amount'              => 'required|numeric|min:1',
            'payment_method'      => 'required|string|max:255',
            'payment_proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['user_id'] = auth('sanctum')->id();
        $validated['reference'] = 'REF-' . time() . rand(1000, 9999);
        $validated['status'] = 'pending';
        $validated['description'] = 'membership_fee';

        // upload proof
        if ($request->hasFile('payment_proof_image')) {
            $image = $request->file('payment_proof_image');
            $imageName = time() . '_membership.jpg';

            if (!file_exists(public_path('images/payments'))) {
                mkdir(public_path('images/payments'), 0777, true);
            }

            $manager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );

            $manager->read($image)
                ->scale(width: 800)
                ->toJpeg(80)
                ->save(public_path('images/payments/' . $imageName));

            $validated['payment_proof_image'] = 'images/payments/' . $imageName;
        }

        $payment = Payment::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Membership payment submitted successfully',
            'data' => $payment
        ], 201);
    }


  

public function paidMember($userId)
{
    
    $paidMember = Membership::where('user_id', $userId)
        ->where('status', 'active')
        ->where('expiry_date', '>', now())
        ->latest()
        ->first(); 

    
    if (!$paidMember) {
       return response()->json([
            'status' => 'error',
            'message' => 'This user does not have an active paid membership.'
        ], 404);   
    }

    return response()->json([
        'status' => 'success',
        'data' => $paidMember,
    ], 200); 
}
}