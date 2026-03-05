<?php

namespace App\Repositories;

use App\Models\Matchs;
use App\Models\MatchPlayer;

class MatchRepository
{
    public function create(array $data): Matchs
    {
        return Matchs::create($data);
    }

    public function insertPlayers(array $players): void
    {
        MatchPlayer::insert($players);
    }

    public function findById(int $matchId): ?Matchs
    {
        return Matchs::find($matchId);
    }

    public function findWithPlayers(int $matchId): Matchs
    {
        return Matchs::with([
            'players.hero',
            'players.role',
            'players.player'
        ])->findOrFail($matchId);
    }

    public function historyByUser(int $userId)
    {
        return Matchs::with([
            'players.hero',
            'players.role',
            'players.player'
        ])
        ->where('user_id', $userId)
        ->latest()
        ->get();
    }
}