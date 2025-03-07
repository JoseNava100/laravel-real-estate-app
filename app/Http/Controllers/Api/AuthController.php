<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'rol_id' => $request->rol_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $response = [
            'token' => $user->createToken('Register_Token:')->plainTextToken,
            'rol_id' => $request->rol_id,
            'name' => $user->name,
            'email' => $user->email,
        ];

        return response()->json([
            'message' => 'User created successfully',
            'data' => $response,
            'status' => 201,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            $response = [
                'token' => $user->createToken('Login_Token:')->plainTextToken,
                'name' => $user->name,
                'email' => $user->email,
            ];

            return response()->json([
                'message' => 'Login successful',
                'data' => $response,
                'status' => 201,
            ], 201);
        }

        return response()->json([
            'message' => 'Authentication error',
            'status' => 400,
        ], 400);
    }

    public function logout(): JsonResponse
    {

        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful',
            'status' => 201,
        ], 201);
    }
}
