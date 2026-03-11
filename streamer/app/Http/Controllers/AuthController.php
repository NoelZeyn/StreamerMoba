<?php

namespace App\Http\Controllers;

use App\DTO\CreateUserDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Exception;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}
    public function validateCaptcha(string $captchaInput): bool
    {
        $captchaStored = Cache::get(
            'captcha_' . request()->ip()
        );

        return $captchaStored && $captchaInput == $captchaStored;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $key = 'register:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {

            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'message' => "Too many registrations. Try again in {$seconds} seconds."
            ], 429);
        }

        if (!$this->validateCaptcha($request->captcha)) {
            return response()->json([
                'message' => 'Captcha is invalid'
            ], 422);
        }

        try {
            $validated = $request->validated();
            $dto = new CreateUserDTO(
                $validated['name'],
                $validated['email'],
                $validated['password'],
                $validated['channel_name']
            );

            $user = $this->authService->register($dto);

            RateLimiter::hit($key, 600);
            return $this->success($user, 'User registered successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function captcha(): JsonResponse
    {
        $code = rand(10000, 99999);

        Cache::put(
            'captcha_' . request()->ip(),
            $code,
            now()->addMinutes(5)
        );

        return response()->json([
            'captcha' => $code
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $key = 'login:' . $request->email . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'message' => "Too many login attempts. Try again in {$seconds} seconds."
            ], 429);
        }

        if (!$this->validateCaptcha($request->captcha)) {
            return response()->json([
                'message' => 'Captcha is invalid'
            ], 422);
        }

        try {

            $token = $this->authService->login(
                $request->email,
                $request->password
            );

            Cache::forget('captcha_' . $request->ip());

            RateLimiter::clear($key);

            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully',
                'access_token' => $token,
                'token_type' => 'bearer'
            ]);
        } catch (Exception $e) {

            RateLimiter::hit($key, 600);

            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
    }

    public function logout(): JsonResponse
    {
        try {

            $this->authService->logout();

            return response()->json([
                'message' => 'User logged out successfully'
            ]);
        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
