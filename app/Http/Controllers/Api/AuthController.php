<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Register user baru
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => __('messages.auth.name_required'),
            'email.required' => __('messages.auth.email_required'),
            'email.email' => __('messages.auth.email_invalid'),
            'email.unique' => __('messages.auth.email_exists'),
            'password.required' => __('messages.auth.password_required'),
            'password.min' => __('messages.auth.password_min'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation_error'),
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => __('messages.auth.register_success'),
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => __('messages.auth.email_required'),
            'email.email' => __('messages.auth.email_invalid'),
            'password.required' => __('messages.auth.password_required'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation_error'),
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.auth.login_failed')
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.server_error')
            ], 500);
        }

        $user = Auth::user();

        return response()->json([
            'success' => true,
            'message' => __('messages.auth.login_success'),
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]
        ]);
    }

    /**
     * Logout user
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            
            return response()->json([
                'success' => true,
                'message' => __('messages.auth.logout_success')
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.server_error')
            ], 500);
        }
    }

    /**
     * Refresh token
     */
    public function refresh()
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());
            
            return response()->json([
                'success' => true,
                'message' => __('messages.success'),
                'data' => [
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60
                ]
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.auth.token_invalid')
            ], 401);
        }
    }

    /**
     * Get user profile
     */
    public function profile()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            return response()->json([
                'success' => true,
                'message' => __('messages.success'),
                'data' => [
                    'user' => $user
                ]
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.auth.token_invalid')
            ], 401);
        }
    }
}
