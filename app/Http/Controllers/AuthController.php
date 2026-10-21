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
use Illuminate\Validation\Rules\Password;

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
        'password' => 'required|min:6|confirmed',
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'gender' => 'required|in:male,female',
        'phone' => 'required|string|max:14',
        'zone_id' => 'required|exists:zones,id', 
    ]);

    $imagePath = null;
    if ($request->hasFile('profile_image')) {
        $image = $request->file('profile_image');

        $imageName = time() . '_' . $request->first_name . '.jpg';

        if (!file_exists(public_path('images/profiles'))) {
            mkdir(public_path('images/profiles'), 0777, true);
        }

        $manager = new ImageManager(new Driver());
        $manager->read($image)
            ->cover(500, 500)
            ->toJpeg(80)
            ->save(public_path('images/profiles/' . $imageName));

        $imagePath = 'images/profiles/' . $imageName;
    }

    $user = User::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'profile_image' => $imagePath,
        'gender' => $request->gender,
        'phone' => $request->phone,
        'zone_id' => $request->zone_id,
        'status' => 'pending',
        'payment_status' => false,
    ]);

    $token = $user->createToken('auth')->plainTextToken;
    $user->notify(new MemberNotification);
    
     $NewMemberNotPaid = false;
        if ($user->membership_id==null && $user->status=='pending')
            {
                $NewMemberNotPaid = true;
            }

    return response()->json([
        'message' => 'Registration successful. Please verify your email.',
        'status' => true,
        'token' => $token,
        'user' => $user,
         'NewMemberNotPaid' => $NewMemberNotPaid
    ], 201);
}


    public function profileUpdate(Request $request)
{
    $user = $request->user();
    
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthenticated. You must be logged in to update your profile.'
        ], 401);
    }

    $validate = $request->validate([
        'first_name'    => 'sometimes|string|max:255',
        'last_name'     => 'sometimes|string|max:255',
        'email'         => 'sometimes|email|unique:users,email,' . $user->id,
        'phone'         => 'sometimes|string|max:14',
    ]);

    

    
    $user->update($validate);

    return response()->json([
        'status' => 'success',
        'message' => 'Profile updated successfully',
    ], 200);
}


 
 public function profilePicture(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthenticated.'
        ], 401);
    }

    $request->validate([
        'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Delete old profile image if it exists
    if ($user->profile_image && file_exists(public_path($user->profile_image))) {
        @unlink(public_path($user->profile_image));
    }

    $image = $request->file('profile_image');

    $nameSlug = str_replace(' ', '_', strtolower($user->first_name));
    $imageName = time() . '_' . $nameSlug . '.jpg';

    $profilesDir = public_path('profiles');
    if (!file_exists($profilesDir)) {
        mkdir($profilesDir, 0755, true);
    }

    $destinationPath = $profilesDir . '/' . $imageName;

    $this->cropAndResizeImage($image->getRealPath(), $destinationPath, 500, 500, 80);

    $user->update([
        'profile_image' => 'profiles/' . $imageName,
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Profile picture updated successfully',
        'user' => $user
    ], 200);
}

/**
 * Crop an image to a square (center crop) and resize it using GD,
 * then save it as a JPEG at the given quality.
 */
private function cropAndResizeImage(string $sourcePath, string $destinationPath, int $width, int $height, int $quality = 80): void
{
    $imageInfo = getimagesize($sourcePath);
    $mime = $imageInfo['mime'];

    // Load the source image based on its real MIME type
    $source = match ($mime) {
        'image/jpeg' => imagecreatefromjpeg($sourcePath),
        'image/png'  => imagecreatefrompng($sourcePath),
        'image/gif'  => imagecreatefromgif($sourcePath),
        default => throw new \RuntimeException('Unsupported image type: ' . $mime),
    };

    $srcWidth = imagesx($source);
    $srcHeight = imagesy($source);

    // Calculate the crop box to get a centered square (like cover()/fit())
    $srcRatio = $srcWidth / $srcHeight;
    $targetRatio = $width / $height;

    if ($srcRatio > $targetRatio) {
        // Source is wider than target — crop the sides
        $cropHeight = $srcHeight;
        $cropWidth = (int) ($srcHeight * $targetRatio);
        $cropX = (int) (($srcWidth - $cropWidth) / 2);
        $cropY = 0;
    } else {
        // Source is taller than target — crop top/bottom
        $cropWidth = $srcWidth;
        $cropHeight = (int) ($srcWidth / $targetRatio);
        $cropX = 0;
        $cropY = (int) (($srcHeight - $cropHeight) / 2);
    }

    // Create the destination canvas
    $destination = imagecreatetruecolor($width, $height);

    // Preserve transparency for PNG/GIF before flattening to JPEG background
    $white = imagecolorallocate($destination, 255, 255, 255);
    imagefill($destination, 0, 0, $white);

    imagecopyresampled(
        $destination,
        $source,
        0, 0,
        $cropX, $cropY,
        $width, $height,
        $cropWidth, $cropHeight
    );

    imagejpeg($destination, $destinationPath, $quality);

    imagedestroy($source);
    imagedestroy($destination);
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
        
         $NewMemberNotPaid = false;
        if ($user->membership_id==null && $user->status=='pending')
            {
               $NewMemberNotPaid = true;
            }
        
        
        return response()->json([
            'message' => 'Login successful',
            'status' => true,
            'token' => $token,
            'user' => $user,
             'NewMemberNotPaid' => $NewMemberNotPaid
        ]);
    }
    
    
    public function logout(Request $request)
    {
         if ($request->user()?->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully'
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



 public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', Password::defaults()],
        ]);



        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Incorrect password.'
            ], 422);
        }
        $request->user()->currentAccessToken()->delete();

        $user->update([
            'password' => Hash::make($request->password)
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Password updated successfully.',
            'token' => $token
        ]);
    }




}
