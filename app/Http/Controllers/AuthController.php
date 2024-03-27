<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'ACTIVE',
        ];

        if (! Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->tokens()->delete();
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return response()->json(['token' => $tokenResult, 'user' => $user->only(['id', 'role_id', 'full_name', 'organization_id'])]);
        } else {
            return response()->json([
                'message' => 'No user found',
            ], 404);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();
        }

        return response()->json(['message' => __('user deconnected')], 200);
    }
}
