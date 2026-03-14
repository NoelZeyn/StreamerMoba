<?php

namespace App\Http\Controllers;

use App\DTO\CreateSeasonDTO;
use App\Http\Requests\CreateSeasonRequest;
use App\Services\SeasonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function __construct(
        protected SeasonService $seasonService
    ) {}

    public function index()
    {
        try {
            $seasons = $this->seasonService->list();
            return $this->success($seasons, 'Seasons retrieved successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSeasonRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $dto = new CreateSeasonDTO(
                $validated['name'],
                $validated['start_date'],
                $validated['end_date'],
                $validated['is_active']
            );
            $season = $this->seasonService->createSeason($dto);
            return $this->success($season, 'Season created successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $season = $this->seasonService->getSeason($id);
            return $this->success($season, 'Season retrieved successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateSeasonRequest $request, int $id)
    {
        try {
            $this->seasonService->updateSeason($id, $request->all());
            return $this->success($request->all(), 'Season updated successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $this->seasonService->deleteSeason($id);
            return $this->success(null, 'Season deleted successfully');
        } catch (\Throwable $th) {
             return $this->error($th->getMessage(), 400);
        }
    }
}
