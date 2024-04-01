<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Organization;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * dfgdfgdgf
 *
 * dfgdfgdfgdf dsfg sdfgsdf gsd gsdfg
 */
class AuthController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'ACTIVE',
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->tokens()->delete();
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                /** @var string $tokenResult The bearer token supplied for this user */
                'token' => $tokenResult,

                // 'user' => $user->only(['id', 'role_id', 'full_name', 'organization_id'])]);
                'user' => [
                    /** @var string The id (uuid) of the connected user */
                    'id' => $user->id,
                    /** @var string The role of the connected user */
                    'role_id' => $user->role_id,
                    /** @var string The full name of the connected user */
                    'full_name' => $user->full_name,
                    /** @var string The id of the organization of the connected user (uuid) */
                    'organization_id' => $user->organization_id,
                ],
            ]);
        } else {
            return response()->json([
                'message' => 'No user found',
            ], 404);
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();
        }

        return response()->json(['message' => __('user deconnected')], 200);
    }

    /**
     * Register
     *
     * # Register a new user for an organization. 
     * Everyone can register on an organization ! Suffit *juste* d'avoir l'uuid de l'organization et **c'est parti mon kiki** ! 
     * 
     * Bien evidemment c'est pas comme ça qu'il faudrait faire dans la vraie vie. C'est juste pour apprendre hein !
     * 
     * Cette parti peut recevoir du mardown, pratique!
     * 
     * scramble c'est vraiment génial pour faire de la doc api en un minimum de temps. Quelle productivité ! [https://github.com/dedoc/scramble](https://github.com/dedoc/scramble)
     */
    public function register(RegisterRequest $request, Organization $organization): UserResource|JsonResponse
    {

        try {
            $user = $this->userRepository->insert($organization, $request->all(), 'USER', 'ACTIVE');

            return new UserResource($user);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 422);
        }
    }
}
