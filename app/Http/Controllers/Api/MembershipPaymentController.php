<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\MembershipFee;
use Illuminate\Http\Request;
use App\Models\Payment;

class MembershipPaymentController extends Controller
{
  



  

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