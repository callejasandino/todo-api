<?php

namespace App\Services;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public static function login(LoginRequest $request)
    {
        //process for logging in to the platform

        $user = User::where('email', $request->input('email'))->first();

        if(!$user) {
            return response()->json([
                'error' => 'User not Found!'
            ], 404);
        }

        if ($user && (Hash::check($request->input('password'), $user->password))) {
            $tokeneable = Hash::make($user);
        } else {
            return response()->json([
                'error' => 'Either Email is not associated to any user or incorrect password.',
            ], 422);
        }

        return response()->json([
            'user' => $user,
            'token' => $user->createToken($tokeneable)->plainTextToken
        ], 200);
    }

    public static function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('authToken')->plainTextToken,
        ], 200);
    }

    public static function validateToken()
    {
        $validated = Auth::user() ? true : false;

        return response()->json([
            'validated' => $validated
        ]);
    }

    public static function logout()
    {
        $authUser = Auth::user();

        $authUser->tokens()->delete();

        return response()->json([
            'message' => 'logged out',
        ], 204);
    }
}
