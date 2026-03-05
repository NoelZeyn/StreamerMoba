<?php

namespace App\Repositories;

use App\Models\Player;

class PlayerRepository
{

    public function create(array $data): Player
    {
        return Player::create($data);
    }

    public function find(int $id): ?Player
    {
        return Player::with('wallet')->find($id);
    }

    public function findOrFail(int $id): Player
    {
        return Player::findOrFail($id);
    }

    public function all()
    {
        return Player::with('wallet')->get();
    }

    public function update(Player $player, array $data): Player
    {
        $player->update($data);
        return $player;
    }

    public function delete(Player $player): bool
    {
        return $player->delete();
    }

    public function findByName(string $name): ?Player
    {
        return Player::where('name', $name)->first();
    }
}