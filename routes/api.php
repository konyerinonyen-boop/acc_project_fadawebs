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
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\MembersFeeController;
use App\Http\Controllers\Api\MembershipPaymentController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaymentHistoryController;
use App\Http\Controllers\Api\BankAccountController;

use Illuminate\Http\Request;




Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working'
    ]);
});


// Invoice


Route::middleware('auth:sanctum')->get('/payments/{id}/invoice', [InvoiceController::class, 'show']);

Route::prefix('auth')->controller(AuthController::class)->group( function () {
  Route::post('register', 'register');
  Route::post('login', 'login' );
  Route::post('send-reset-link', 'sendResetLink' );
  Route::post('reset-password', 'resetPassword' );
  Route::post('set-password', 'setPassword' );
});

Route::middleware('auth:sanctum')->prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('logout', 'logout');
    Route::post('changePassword', 'changePassword');
});



//  All Zones
 Route::get('/zone', [ZoneController::class, 'index']);



// Events && Blogs That belong to a zone with their details
Route::prefix('dashboard')->middleware('auth:sanctum')->group( function () {
    Route::post('/changePicture', [AuthController::class, 'profilePicture']);
    Route::post('/updateProfile', [AuthController::class, 'profileUpdate']);
    Route::get('/resources', [ResourceController::class, 'index']);
    Route::get('/resources/{id}', [ResourceController::class, 'detailResource']);
});

//Members
Route::prefix('members')->group(function() {
 Route::get('/', [MemberController::class, 'index']);
 Route::get('/search', [MemberController::class, 'search']);
 Route::get('/{id}', [MemberController::class, 'detailMember']);
 Route::get('/{id}/qr', [MemberController::class, 'qr']);
});


Route::get('/zone/{zoneId}/blogs', [ZonalPostController::class, 'zoneBlogs']);
Route::get('/zone/{zoneId}/blogs/{blogId}', [ZonalPostController::class, 'detailPost']);
Route::get('/zone/{zoneId}/event', [EventPostController::class, 'zoneEvent']);
Route::get('/zone/{zoneId}/event/{eventId}', [EventPostController::class, 'detailEvent']);


// All Events and detail event = featured events from the super admin
// Route::prefix('event')->group(function (){
//  Route::get('/', [EventController::class, 'index']);
//  Route::post('/attendance', [AttendanceController::class, 'store']);
// Route::get('/{eventId}', [EventController::class, 'getEvent']);


// // upcoming events
// Route::get('/upcoming', [EventController::class, 'upcomingEvents']);
// Route::get('/upcoming/{eventId}', [EventController::class, 'upcomingEventsDetail']);

// // past events
// Route::get('/past', [EventController::class, 'pastEvents']);
// });


Route::prefix('event')->group(function () {

    Route::get('/', [EventController::class, 'index']);
    Route::get('/search', [EventController::class, 'search']);

    Route::post('/attendance', [AttendanceController::class, 'store']);

    // upcoming events (MUST come before /{eventId})
    Route::get('/upcoming', [EventController::class, 'upcomingEvents']);
    Route::get('/upcoming/{eventId}', [EventController::class, 'upcomingEventsDetail']);

    // past events
    Route::get('/past', [EventController::class, 'pastEvents']);

    // wildcard route MUST be last
    Route::get('/{eventId}', [EventController::class, 'getEvent']);
});


// All Blogs and detail blog
Route::prefix('blog')->group( function () {
    Route::get('/', [ZonalPostController::class,'allPost']);
    Route::get('/{Id}', [ZonalPostController::class, 'getBlog']);
});


// Manual payment

Route::prefix('payment')->controller(PaymentController::class)->group(function() {
    Route::middleware('auth:sanctum')->post('/pay', 'pay');
    Route::get('/paymentType', 'paymentType');
    Route::get('/plans', 'plans');

    Route::middleware('auth:sanctum')->get('/donationSummary', 'donationSummary');
    Route::middleware('auth:sanctum')->get('/memberFeeSummary', 'memberFeeSummary');
    Route::middleware('auth:sanctum')->get('/calculateImpactPoints', 'calculateImpactPoints');
});


Route::middleware('auth:sanctum')->group(function () {

    // Endpoint for single member status
    Route::get('/members/{user}/status', [MemberController::class, 'memberStatus']);

    // Endpoint for the zone breakdown
  //  Route::get('/zones/{zoneId}/summary', [MemberController::class, 'zoneSummary']);

});

Route::middleware('auth:sanctum')->get('/dashboard', [DashboardController::class, 'index']);




Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard/dues-payment', [PaymentHistoryController::class, 'duesPayments']);
    Route::get('/dashboard/donation-payments', [PaymentHistoryController::class, 'donationPayments']);


});

// Invoice


Route::middleware('auth:sanctum')->get('/payments/{id}/invoice', [InvoiceController::class, 'show']);

// Accounts

Route::get('/bank-accounts', [BankAccountController::class, 'accounts']);
