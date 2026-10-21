<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Notifications\EventRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'event_id'      => 'required|exists:events,id',
            'first_name'    => 'nullable|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'phone_number'  => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
            'home_address'  => 'nullable|string|max:255',
            'gender'        => 'nullable|in:male,female',
        ]);

        // Attach logged-in user automatically, if authenticated. Null for guests.
        if (Schema::hasColumn('attendances', 'user_id')) {
            $validatedData['user_id'] = $request->user()?->id;
        }

        // Optional: prefill from user profile if logged in and not supplied
        if ($request->user()) {
            $user = $request->user();
            $validatedData['first_name'] = $validatedData['first_name'] ?? $user->first_name ?? $user->name;
            $validatedData['email']      = $validatedData['email'] ?? $user->email;
        }

        $generatedId = 'ATT-' . strtoupper(Str::random(6));

        while (Attendance::where('attendance_id', $generatedId)->exists()) {
            $generatedId = 'ATT-' . strtoupper(Str::random(6));
        }

        $validatedData['attendance_id'] = $generatedId;

        $attendance = Attendance::create($validatedData);

        if ($attendance->email) {
            Notification::route('mail', $attendance->email)
                ->notify(new EventRegistrationNotification($attendance));
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Attendance recorded successfully!',
            'data'    => $attendance,
        ], 201);
    }
}
