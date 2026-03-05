<?php
namespace App\Http\Controllers;

use App\DTO\CreateUserDTO;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Exception;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {

            $dto = new CreateUserDTO(
                $request->input('name'),
                $request->input('email'),
                $request->input('password'),
                $request->input('channel_name')
            );

            $user = $this->authService->register($dto);

            return response()->json([
                'message' => 'User registered successfully',
                'data' => $user
            ], 201);

        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
        public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->login(
                $request->input('email'),
                $request->input('password')
            );

            return response()->json([
                'message' => 'User logged in successfully',
                'data' => $user
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();

            return response()->json([
                'message' => 'User logged out successfully'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
