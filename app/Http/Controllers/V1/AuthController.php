<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Models\PasswordResetOtp;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\V1\ProfileResource;
use App\Http\Resources\V1\UserResource;
use App\Notifications\Mail\SendCodeResetPassword;
use App\Services\V1\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Lcobucci\JWT\JwtFacade;
use App\Services\V1\StudentNumberService;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController
{
    public function register(CreateUserRequest $request)
    {
        $data = $request->validated();

        $userData = [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'location' => $data['location'] ?? null,
            'approved' => false,
        ];

        if ($request->hasFile('profile_image')) {
            $profile_path = $data['profile_image']->store('users/profile', 'public');
            $url = env('APP_URL') . '/storage/' . $profile_path;
            $userData['profile_image'] = $url;
        }
        if ($data['role'] === 'super_admin') {
            $userData['approved'] = true;
        }

        $user = User::create($userData);
        $user->assignRole($data['role']);

        if ($user->hasRole('super_admin')) {
            $user->superAdmin()->create(
                ['user_id' => $user->id,]
            );
        }
        $user->save();
        $token = JWTAuth::fromUser($user);
        // if($user->hasRole('teacher')){
        //     $material = $user->teacher->materials()->get();
        //     $salary = $user->teacher->salaries()->get();
        //     $section = $user->teacher->sections()->get();
        //     $grade = $user->teacher->sections()->with('grade')->get()->pluck('grade')->unique('id');
        // }
        // if($user->hasRole('admin')) {
        //     $salary = $user->admin->salaries()->get();
        // }

        return response()->json([
            'user' =>  new UserResource($user),
            'token' => $token,
            // 'material'=> $material ?? null,
            // 'salary'=> $salary ?? null,
            // 'section'=> $section ?? null,
            // 'grade'=>$grade ?? null
        ], 201);
    }


    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|string|min:8',
    //     ]);



    //     $credintials = $request->only('email', 'password');
    //     if (!$token = JWTAuth::attempt($credintials)) {
    //         return response()->json(['error' => 'Unauthorization'], 401);
    //     }
    //     $user = auth('api')->user();


    //     if (!$user->approved) {
    //         JWTAuth::invalidate($token);
    //         return response()->json([
    //             'error' => 'User not approved'
    //         ], 403);
    //     }

    //     return response()->json([
    //         'token' => $token,
    //         'user' => new UserResource($user),
    //     ], 200);
    // }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if (!$token = JWTAuth::attempt($validated)) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }

        $user = auth('api')->user();

        if (!$user->approved) {
            return response()->json([
                'error' => 'User not approved'
            ], 403);
        }

        $material = null;
        $salary = null;
        $section = null;
        $grade = null;

        if ($user->hasRole('teacher') && $user->teacher) {
            $material = $user->teacher->materials()->get();
            $salary   = $user->teacher->salaries()->get();
            $section  = $user->teacher->sections()->get();
            $grade    = $user->teacher->sections()->with('studyStage')->get()->pluck('studyStage')->unique('id')->values();
        }

        if ($user->hasRole('admin') && $user->admin) {
            $salary = $user->admin->salaries()->get();
        }

        return response()->json([
            'token'    => $token,
            'user'     => new ProfileResource($user),
            'material' => $material,
            'salary'   => $salary,
            'section'  => $section,
            'grade'    => $grade
        ], 200);
    }

    public function logout()
    {
        try {
            auth('api')->logout();

            return response()->json([
                'message' => 'Successfully logged out'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to logout, please try again'
            ], 500);
        }
    }

    public function me()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }
        $profile = app(ProfileService::class)->getProfile($user);
        return response()->json([
            'profile' => $profile
        ], 200);
    }

    public function refresh()
    {
        return response()->json([
            'token' => auth()->refresh()
        ]);
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        if (!$user) {
            return response()->json(['message' => 'user not fount'], 404);
        }
        $user->approved = true;
        $user->save();


        if ($user->hasRole('teacher')) {
            $user->teacher()->create(
                ['user_id' => $id,]
            );
        } elseif ($user->hasRole('student')) {
            $user->student()->create(
                [
                    'user_id' => $id,
                    'student_number' => StudentNumberService::generate()
                ]
            );
        } elseif ($user->hasRole('admin')) {
            $user->admin()->create(
                ['user_id' => $id,]
            );
        } elseif ($user->hasRole('super_admin')) {
            $user->super_admin()->create(
                ['user_id' => $id,]
            );
        }
        return response()->json(['message' => 'User approved successfully']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);


        $otp = rand(100000, 999999);

        PasswordResetOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => Hash::make($otp),
                'expires_at' => now()->addMinutes(10),
                'attempts' => 0
            ]
        );
        Mail::to($request->email)->send(new SendCodeResetPassword($otp));

        return response()->json([
            'message' => 'OTP sent successfully'
        ]);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|min:6|confirmed'
        ]);

        $record = PasswordResetOtp::where('email', $request->email)->first();

        if (!$record) {
            return response()->json(['error' => 'OTP not found'], 400);
        }


        if (now()->greaterThan($record->expires_at)) {
            return response()->json(['error' => 'OTP expired'], 400);
        }


        if ($record->attempts >= 5) {
            return response()->json(['error' => 'Too many attempts'], 429);
        }


        if (!Hash::check($request->otp, $record->otp)) {
            $record->increment('attempts');
            return response()->json(['error' => 'Invalid OTP'], 400);
        }

        $user = User::where('email', $request->email)->first();

        $user->password = Hash::make($request->password);

        $user->password_changed_at = now();

        $user->save();

        $record->delete();

        return response()->json([
            'message' => 'Password reset successful'
        ]);
    }
}
