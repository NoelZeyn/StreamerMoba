<?php

namespace App\Repositories;

use App\Models\Hero;
use App\Models\MatchPlayer;

class HeroRepository
{
    public function list()
    {
        return Hero::all();
    } 

    public function create(array $data): Hero
    {
        return Hero::create($data);
    }

    public function findById(int $heroId): ?Hero
    {
        return Hero::find($heroId);
    }

    public function update(Hero $hero, array $data): Hero
    {
        $hero->update($data);
        return $hero;
    }

    public function delete(Hero $hero): bool
    {
        return $hero->delete();
    }

    public function findOrFail(int $heroId): Hero
    {
        return Hero::findOrFail($heroId);
    }
}
