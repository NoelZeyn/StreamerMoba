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

            return $this->created($user, 'User registered successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->login(
                $request->input('email'),
                $request->input('password')
            );
            return $this->success($user, 'User logged in successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();
            return $this->success(null, 'User logged out successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
