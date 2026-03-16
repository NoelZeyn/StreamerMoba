<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\DTO\CreateUserDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
class AuthService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}
    public function register(CreateUserDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {

            $user = $this->userRepository->create([
                'name' => $dto->name,
                'email' => $dto->email,
                'channel_name' => $dto->channel_name,
                'password' => Hash::make($dto->password),
                'webhook_token' => hash('sha256', Str::uuid() . Str::random(10))
            ]);

            // $token = JWTAuth::fromUser($user);

            return [
                'user' => $user,
                // 'token' => $token
            ];
        });
    }
    public function login(string $email, string $password): string
    {
        if (!$token = JWTAuth::attempt([
            'email' => $email,
            'password' => $password
        ])) {
            throw new Exception('Invalid credentials', 401);
        }
        return $token;
    }

    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }
}
