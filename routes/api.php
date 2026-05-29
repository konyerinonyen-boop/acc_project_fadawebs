<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventPostController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\AuthController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ZonalPostController;
use App\Http\Controllers\Api\ZoneController;
use Illuminate\Http\Request;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working'
    ]);
});


// Login && Register
Route::prefix('auth')->controller(AuthController::class)->group( function () {
  Route::post('register', 'register');
  Route::post('login', 'login' );
  Route::post('send-reset-link', 'sendResetLink' );
  Route::post('reset-password', 'resetPassword' );
});



//  All Zones
 Route::get('/zone', [ZoneController::class, 'index']);


// Events && Blogs That belong to a zone with their details
Route::prefix('dashboard')->middleware('auth:sanctum')->group( function () {
    Route::get('/resources', [ResourceController::class, 'index']);
    Route::get('/resources/{id}', [ResourceController::class, 'detailResource']);
});

//Members
Route::prefix('members')->group(function() {
 Route::get('/', [MemberController::class, 'index']);
 Route::get('/{id}', [MemberController::class, 'detailMember']);
});


Route::get('/zone/{zoneId}/blogs', [ZonalPostController::class, 'zoneBlogs']);
Route::get('/zone/{zoneId}/blogs/{blogId}', [ZonalPostController::class, 'detailPost']);
Route::get('/zone/{zoneId}/event', [EventPostController::class, 'zoneEvent']);
Route::get('/zone/{zoneId}/event/{eventId}', [EventPostController::class, 'detailEvent']);


// All Events and detail event 
Route::prefix('event')->group(function (){
 Route::get('/', [EventController::class, 'index']);
Route::get('/{eventId}', [EventController::class, 'getEvent']);
});



// All Blogs and detail blog
Route::prefix('blog')->group( function () {
    Route::get('/', [ZonalPostController::class,'allPost']);
    Route::get('/{Id}', [ZonalPostController::class, 'getBlog']); 
});


