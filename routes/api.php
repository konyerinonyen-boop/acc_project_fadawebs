<?php

use App\Http\Controllers\AuthController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;

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
