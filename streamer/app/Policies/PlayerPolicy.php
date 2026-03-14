<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Player;

class PlayerPolicy
{
    public function viewAny(User $user): bool
    {
        // Berikan true agar user yang login bisa melihat daftar player-nya
        return true;
    }

    public function view(User $user, Player $player): bool
    {
        return $player->user_id === $user->id;
    }

    public function update(User $user, Player $player): bool
    {
        return $player->user_id === $user->id;
    }

    public function delete(User $user, Player $player): bool
    {
        return $player->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }
}
