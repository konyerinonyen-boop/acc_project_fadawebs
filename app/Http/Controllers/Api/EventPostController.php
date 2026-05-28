<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;

class EventPostController extends Controller
{
    public function zoneEvent(string $zoneId)
{
    $events = Event::with(['schedules', 'speakers', 'zone', 'user'])
        ->where('zone_id', $zoneId)
        ->get();

        if($events->isEmpty()){
           return response()->json([
              'status' => 'error',
            'message' => 'No events found for this zone'
           ]);
        }

    return response()->json([
        'status' => 'success',
        'count' => $events->count(),
        'data' => $events
    ],200);
}

  
public function detailEvent(string $zoneId, string $eventId){
   $event = Event::with(['schedules', 'speakers', 'user', 'zone'])
    ->where('zone_id', $zoneId)
    ->where('id', $eventId)
    ->first();

    
        if(!$event){
           return response()->json([
              'status' => 'error',
            'message' => 'No events found for this zone'
           ],404);
        }

    return response()->json([
    'status' => 'success',
    'data' => $event
], 200);
}


}
