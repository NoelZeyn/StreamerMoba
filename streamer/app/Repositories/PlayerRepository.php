<?php

namespace App\Repositories;

use App\Models\Player;
use App\Models\VipWallet;

class PlayerRepository
{

    public function create(array $data): Player
    {
        return Player::create($data);
    }

    public function createWallet(int $playerId, int $playBalance = 0)
    {
        return VipWallet::create([
            'player_id' => $playerId,
            'play_balance' => $playBalance
        ]);
    }

    public function find(int $id): ?Player
    {
        return Player::with('wallet')->find($id);
    }

    public function getAll()
    {
        return Player::with('wallet')->get();
    }

}