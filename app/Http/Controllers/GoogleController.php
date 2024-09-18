<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class GoogleController extends Controller
{
    /**
     * Redirect to Google OAuth.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function redirect()
    {
        $redirectUrl = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        return response()->json(['url' => $redirectUrl]);
    }

    /**
     * Handle Google callback and return JWT token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function callbackGoogle()
    {
        try {
            $google_user = Socialite::driver('google')->stateless()->user();
            $user = User::where('google_id', $google_user->getId())->first();

            if (!$user) {
                // Create a new user if it doesn't exist
                $user = User::create([
                    'first_name' => $google_user->getName(),
                    'email' => $google_user->getEmail(),
                    'google_id' => $google_user->getId(),
                    // Generate a password placeholder for completeness
                    'password' => bcrypt('google')
                ]);
            }

            // Generate JWT token for the authenticated user
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'role' => auth()->user()->role
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
