<?php

namespace App\Services;

use App\Repositories\SeasonRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class SeasonService
{

    public function __construct(
        protected SeasonRepository $seasonRepository,
    ) {}

    public function list()
    {
        return $this->seasonRepository->list();
    }

    public function createSeason($dto)
    {
        return DB::transaction(function () use ($dto) {
            $season = $this->seasonRepository->create([
                'name' => $dto->name,
                'start_date' => $dto->start_date,
                'end_date' => $dto->end_date,
                'is_active' => $dto->is_active
            ]);
            return $season;
        });
    }

    public function updateSeason($id, $dto)
    {
        return DB::transaction(function () use ($id, $dto) {
            $season = $this->seasonRepository->findOrFail($id);
            $season = $this->seasonRepository->update($season, [
                'name' => $dto->name,
                'start_date' => $dto->start_date,
                'end_date' => $dto->end_date,
                'is_active' => $dto->is_active
            ]);

            return $season;
        });
    }

    public function deleteSeason($id)
    {
        return DB::transaction(function () use ($id) {
            $season = $this->seasonRepository->findOrFail($id);
            $this->seasonRepository->delete($season);
            return true;
        });
    }

    public function getSeason($id)
    {
        $season = $this->seasonRepository->findOrFail($id);
        return $season;
    }
}
