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
                $validated['channel_name'],
            );

            $user = $this->authService->register($dto);

            RateLimiter::hit($key, 600);
            return $this->success($user, 'User registered successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function captcha()
    {
        $code = (string) rand(10000, 99999);
        Cache::put(
            'captcha_' . request()->ip(),
            $code,
            now()->addMinutes(5)
        );
        $width = 120;
        $height = 40;
        $image = imagecreate($width, $height);

        $background = imagecolorallocate($image, 240, 240, 240);
        $textColor = imagecolorallocate($image, 0, 80, 150);
        $noiseColor = imagecolorallocate($image, 100, 100, 100);

        for ($i = 0; $i < 10; $i++) {
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $noiseColor);
        }

        imagestring($image, 5, 35, 12, $code, $textColor);

        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return response($imageData)->header('Content-Type', 'image/png');
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
