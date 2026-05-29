<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
  public function index()
{
    
    $members = User::whereNotNull('membership_id')
        ->where('status', 'approved') 
        ->get();

    if ($members->isEmpty()) {
        return response()->json([
            'message' => 'No active members found'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => $members
    ], 200);
}

public function detailMember($id){
    $member = User::whereNotNull('membership_id')
        ->where('status', 'approved') 
        ->find($id);

   if (!$member) {
        return response()->json([
            'message' => 'No active members found'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => $member
    ], 200);
}
}
