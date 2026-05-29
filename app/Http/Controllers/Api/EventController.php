<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(){
        $events = Event::with('user')
        ->where('user_id', 1)
        ->get();
        if($events->isEmpty()){
          return response()->json([
          'message' => 'No events found from the Super Admin'
          ], 404);
        }

         return response()->json([
            'status' => 'success',
            'data' => $events
          ], 200);
    }

    public function getEvent($id){
      $event = Event::with(['schedules', 'speakers', 'user','zone'])
      ->where("user_id", 1)
      ->find($id);

      if(!$event){
         return response()->json([
          'message' => 'No event found or this event was not posted by the Super Admin'
          ], 404);
      }


      return response()->json([
            'status' => 'success',
            'data' => $event
          ], 200);

    }

  

}
