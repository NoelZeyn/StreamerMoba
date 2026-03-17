<?php

namespace App\Repositories;

use App\Models\Season;
use App\Models\MatchPlayer;

class SeasonRepository
{
    public function list()
    {
        return Season::orderBy('id', 'desc')->get();
    }

    public function create(array $data): Season
    {
        return Season::create($data);
    }

    public function findById(int $seasonId): ?Season
    {
        return Season::find($seasonId);
    }

    public function update(Season $season, array $data): Season
    {
        $season->update($data);
        return $season;
    }

    public function delete(Season $season): bool
    {
        return $season->delete();
    }

    public function findOrFail(int $seasonId): Season
    {
        return Season::findOrFail($seasonId);
    }
}
