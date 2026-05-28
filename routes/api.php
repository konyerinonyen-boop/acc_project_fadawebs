<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventPostController;
use App\Http\Controllers\AuthController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ZonalPostController;
use App\Http\Controllers\Api\ZoneController;
use Illuminate\Support\Facades\Request;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working'
    ]);
});
Route::prefix('auth')->controller(AuthController::class)->group( function () {
  Route::post('register', 'register');
  Route::post('login', 'login' );
  Route::post('send-reset-link', 'sendResetLink' );
  Route::post('reset-password', 'resetPassword' );
});


// Route::prefix('posts')->controller(ZonalPostController::class)->group( function () {
//     Route::get('/zone/{zone_name}', 'zonalPosts');
//     Route::get('/', 'allPosts');
//     Route::get('/{postId}/zone/{zoneId}', 'detailPost');
// });


 

Route::prefix('zones')->group( function () {
    Route::get('/', [ZoneController::class, 'index']);
    
    Route::get('/{zoneId}/blogs', [ZonalPostController::class, 'zoneBlogs']);
    Route::get('/{zoneId}/blogs/{postId}', [ZonalPostController::class, 'detailPost']);
     Route::get('/event', [EventController::class, 'index']);
    Route::get('/{zoneId}/event', [EventPostController::class, 'zoneEvent']);
    Route::get('/{zoneId}/event/{eventId}', [EventPostController::class, 'detailEvent']);
});

