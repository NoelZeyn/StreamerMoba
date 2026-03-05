<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\DTO\CreateUserDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function register(CreateUserDTO $dto)
    {
        return DB::transaction(function () use ($dto) {

            $user = $this->userRepository->create([
                'name' => $dto->name,
                'email' => $dto->email,
                'channel_name' => $dto->channel_name,
                'password' => Hash::make($dto->password)
            ]);

            Auth::login($user);

            return $user;
        });
    }

    public function login(string $email, string $password)
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            throw new Exception('Invalid credentials');
        }

        Auth::login($user);

        return $user;
    }

    public function logout()
    {
        Auth::logout();
    }
}