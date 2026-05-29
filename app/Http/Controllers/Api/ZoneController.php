<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Zone;

class ZoneController extends Controller
{
    public function index(){
        $zones = Zone::all();
        if($zones->isEmpty()){
            return response()->json([
                'message' => 'No zones found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $zones
        ], 200);
    }

    
}
