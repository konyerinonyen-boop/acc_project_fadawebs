<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
  
public function manualDonation(Request $request)
{
    
    $validatedData = $request->validate([
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


}
