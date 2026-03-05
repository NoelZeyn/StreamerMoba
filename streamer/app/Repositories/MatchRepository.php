<?php

namespace App\Repositories;

use App\Models\Matchs;
use App\Models\MatchPlayer;

class MatchRepository
{

    public function createMatch(array $data): Matchs
    {
        return Matchs::create($data);
    }

    public function insertPlayers(array $players): void
    {
        MatchPlayer::insert($players);
    }

    public function getMatchHistory(int $userId)
    {
        return Matchs::with('players.hero', 'players.role', 'players.player')
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }
}