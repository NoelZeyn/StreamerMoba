<?php

namespace App\Http\Controllers;

use App\DTO\CreateUserDTO;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Http;
use Exception;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $key = 'register:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return $this->error(
                "Too many registrations. Try again in {$seconds} seconds.",
                429
            );
        }

        try {
            $dto = new CreateUserDTO(
                $request->input('name'),
                $request->input('email'),
                $request->input('password'),
                $request->input('channel_name')
            );
            $user = $this->authService->register($dto);

            RateLimiter::hit($key, 600);
            return $this->created($user, 'User registered successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }


    public function login(LoginRequest $request): JsonResponse
    {
        $key = 'login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return $this->error(
                "Too many login attempts. Try again in {$seconds} seconds.",
                429
            );
        }

        // validasi captcha
        // $captchaToken = $request->input('captcha_token');

        // if (!$captchaToken) {
        //     return $this->error('Captcha token missing', 422);
        // }

        // if (!$this->verifyCaptcha($captchaToken)) {
        //     return $this->error('Captcha verification failed', 422);
        // }

        try {

            $user = $this->authService->login(
                $request->input('email'),
                $request->input('password')
            );

            RateLimiter::clear($key);

            return $this->success($user, 'User logged in successfully');
        } catch (Exception $e) {

            RateLimiter::hit($key, 600);

            return $this->error('Invalid credentials', 401);
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
    // private function verifyCaptcha(string $token): bool
    // {
    //     $response = Http::asForm()->post(
    //         'https://www.google.com/recaptcha/api/siteverify',
    //         [
    //             'secret' => config('services.recaptcha.secret'),
    //             'response' => $token,
    //         ]
    //     );
    //     return $response->json('success') === true;
    // }
}
