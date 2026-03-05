<?php

namespace App\Http\Controllers;

use App\DTO\CreateMatchDTO;
use App\DTO\MatchPlayerDTO;
use App\Http\Requests\CreateMatchRequest;
use App\Services\MatchService;
use Illuminate\Http\JsonResponse;
use Exception;

class MatchController extends Controller
{
    public function __construct(
        protected MatchService $matchService
    ) {}

    /**
     * Store new match
     */
    public function store(CreateMatchRequest $request): JsonResponse
    {
        try {

            $players = collect($request->input('players'))
                ->map(fn ($player) => new MatchPlayerDTO(
                    $player['player_id'],
                    $player['hero_id'],
                    $player['role_id']
                ))
                ->toArray();

            $dto = new CreateMatchDTO(
                $request->input('season_id'),
                $players
            );

            $match = $this->matchService->createMatch($dto);

            return response()->json([
                'message' => 'Match created successfully',
                'data' => $match
            ], 201);

        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}