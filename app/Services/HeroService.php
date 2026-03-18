<?php

namespace App\Services;

use App\Repositories\HeroRepository;
use Illuminate\Support\Facades\DB;

class HeroService
{

    public function __construct(
        protected HeroRepository $heroRepository,
    ) {}

    public function list()
    {
        return $this->heroRepository->list();
    }

    public function createHero($dto)
    {
        return DB::transaction(function () use ($dto) {
            $hero = $this->heroRepository->create([
                'name' => $dto->name
            ]);
            return $hero;
        });
    }

    public function updateHero($id, $dto)
    {
        return DB::transaction(function () use ($id, $dto) {
            $hero = $this->heroRepository->findOrFail($id);
            $hero = $this->heroRepository->update($hero, [
                'name' => $dto->name
            ]);
            return $hero;
        });
    }

    public function deleteHero($id)
    {
        return DB::transaction(function () use ($id) {
            $hero = $this->heroRepository->findOrFail($id);
            $this->heroRepository->delete($hero);
            return true;
        });
    }

    public function getHero($id)
    {
        $hero = $this->heroRepository->findOrFail($id);
        return $hero;
    }
}
