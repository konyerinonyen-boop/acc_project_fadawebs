<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipFee;
use Illuminate\Http\Request;

class MembersFeeController extends Controller
{
   public function index()
{
    $fees = MembershipFee::where('is_active', true)->get();

    return response()->json([
        'status' => 'success',
        'data'    => $fees
    ], 200);
}
}
