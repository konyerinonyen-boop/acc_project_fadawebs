<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(){
        $events = Event::all();
        if($events->isEmpty()){
          return response()->json([
          'message' => 'No events found'
          ], 404);
        }

         return response()->json([
            'status' => 'success',
            'data' => $events
          ], 200);
    }

}
