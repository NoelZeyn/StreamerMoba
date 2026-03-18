<?php

namespace App\Http\Controllers;

use App\DTO\CreateHeroDTO;
use App\Http\Requests\CreateHeroRequest;
use App\Services\HeroService;
use Illuminate\Http\JsonResponse;

class HeroController extends Controller
{
    public function __construct(
        protected HeroService $heroService
    ) {}

    public function index()
    {
        try {
            $heroes = $this->heroService->list();
            return $this->success($heroes, 'Heroes retrieved successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateHeroRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $dto = new CreateHeroDTO(
                $validated['name']
            );
            $hero = $this->heroService->createHero($dto);
            return $this->success($hero, 'Hero created successfully');
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
            $hero = $this->heroService->getHero($id);
            return $this->success($hero, 'Hero retrieved successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateHeroRequest $request, int $id)
    {
        try {
            $this->heroService->updateHero($id, $request->all());
            return $this->success($request->all(), 'Hero updated successfully');
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
            $this->heroService->deleteHero($id);
            return $this->success(null, 'Hero deleted successfully');
        } catch (\Throwable $th) {
             return $this->error($th->getMessage(), 400);
        }
    }
}
