<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    // ✅ REGISTER API
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:6',
                'role' => 'required|in:user,admin',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'token' => $token,
                'user' => $user,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ✅ LOGIN API
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);


            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.']
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ✅ LOGOUT API
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'message' => 'Logged out successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
