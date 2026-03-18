<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function find(int $id): User
    {
        return User::findOrFail($id);
    }

    public function all()
    {
        return User::get();
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function findByName(string $name): ?User
    {
        return User::where('name', $name)->first();
    }

    public function findByChannelName(string $channelName): ?User
    {
        return User::where('channel_name', $channelName)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}