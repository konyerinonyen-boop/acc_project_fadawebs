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
        ->where('is_featured', true)
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
      ->where('is_featured', true)
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


  public function upcomingEvents(){
    $events = Event::with('user')
    ->where('user_id', 1)
    ->where('date', '>', now())
    ->orderBy('date', 'asc')
    ->get();

    if($events->isEmpty()){
      return response()->json([
      'message' => 'No upcoming events found from the Super Admin'
      ], 404);
    }

     return response()->json([
        'status' => 'success',
        'data' => $events
      ], 200);
  }

  public function upcomingEventsDetail($id){
    $event = Event::with(['schedules', 'speakers', 'user','zone'])
    ->where("user_id", 1)
    ->where('date', '>', now()->toDateString())
    ->find($id);

    if(!$event){
       return response()->json([
        'message' => 'No upcoming event found or this event was not posted by the Super Admin'
        ], 404);
    }

    return response()->json([
          'status' => 'success',
          'data' => $event
        ], 200);
    
    
    }

  public function pastEvents(){
    $events = Event::with('user')
    ->where('user_id', 1)
    ->where('date', '<', now()->toDateString())
    ->orderBy('date', 'desc')
    ->get();

    if($events->isEmpty()){
      return response()->json([
      'message' => 'No past events found from the Super Admin'
      ], 404);
    }

     return response()->json([
        'status' => 'success',
        'data' => $events
      ], 200);
  }


  

}
