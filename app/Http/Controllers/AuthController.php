<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNotification;
use App\Notifications\MemberNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Notifications\PasswordResetNotification;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Register user
     */
    public function register(Request $request)
    {

        $request->validate([
    'first_name' => 'required|string|max:255',
    'last_name' => 'required|string|max:255',

    'email' => 'required|email|unique:users,email',


    'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

    'gender' => 'required|in:male,female',

    'phone' => 'required|string|max:14',

]);
        $imagePath = null;

        if ($request->hasFile('profile_image')) {

            $image = $request->file('profile_image');

            $imageName = time() . '_' . $request->name . '.jpg';

            // Create folder if it does not exist
            if (!file_exists(public_path('images/profiles'))) {
                mkdir(public_path('images/profiles'), 0777, true);
            }

            $manager = new ImageManager(new Driver());

            // Resize image using Intervention Image v3
            $resizedImage = $manager->read($image)
                ->cover(500, 500)
                ->toJpeg(80);

            // Save image
            $resizedImage->save(public_path('images/profiles/' . $imageName));

            $imagePath = 'images/profiles/' . $imageName;
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'profile_image' => $imagePath,
            'gender' => $request->gender,
             'phone' => $request->phone,
             'zone_id' => $request->zone_id,
              'status' => 'pending',
             'payment_status' => false,
        ]);



        return response()->json([
            'message' => 'Registration successful. Please verify your email.',
            'status' => true,

            'user' => $user,
        ], 201);
    }


    public function setPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $token = $request->user()->createToken('auth')->plainTextToken;

        $user->notify(new MemberNotification);

        return response()->json([
            'message' => 'Password set successfully',
            'status' => true,
            'token' => $token,
            'user' => $request->user(),
        ]);
    }

    /**
     * Login user
     */


    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->login)
            ->orWhere('first_name', $request->login)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {

            throw ValidationException::withMessages([
                'login' => ['Invalid credentials'],
            ]);
        }

        $token = $user->createToken('auth')->plainTextToken;
        $user->notify(new LoginNotification());
        return response()->json([
            'message' => 'Login successful',
            'status' => true,
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'url' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        $token = Str::random(64);

        // Delete old token
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Save token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        // Reset link
        $resetLink = $request->url
            . "?token={$token}&email={$request->email}";

        // Send notification
        $user->notify(new PasswordResetNotification($resetLink));

        return response()->json([
            'status' => true,
            'message' => 'Password reset link sent to your email',
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {

            return response()->json([
                'message' => 'Invalid or expired reset token',
            ], 400);
        }

        // Check if token expired
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {

            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return response()->json([
                'message' => 'Reset link has expired',
            ], 400);
        }

        // Verify token
        if (!Hash::check($request->token, $record->token)) {

            return response()->json([
                'message' => 'Invalid reset token',
            ], 400);
        }

        // Update password
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Delete token after reset
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password reset successful',
        ]);


    }



    public function user(Request $request)
{
    $user = User::with('zone')->find($request->user());

    return response()->json([
        'status' => true,
        'user' => $user
    ]);
}
}
