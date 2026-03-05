<?php

namespace App\Http\Controllers;

use App\DTO\CreatePlayerDTO;
use App\Http\Requests\CreatePlayerRequest;
use App\Services\PlayerService;
use Illuminate\Http\JsonResponse;
use Exception;

class PlayerController extends Controller
{

    public function __construct(
        protected PlayerService $playerService
    ) {}

    /**
     * List all players
     */
    public function index(): JsonResponse
    {
        $players = $this->playerService->list();

        return response()->json([
            'data' => $players
        ]);
    }

    /**
     * Create player
     */
    public function store(CreatePlayerRequest $request): JsonResponse
    {
        try {

            $dto = new CreatePlayerDTO(
                $request->input('name'),
                $request->input('type')
            );

            $player = $this->playerService->create($dto);

            return response()->json([
                'message' => 'Player created successfully',
                'data' => $player
            ], 201);

        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show player
     */
    public function show(int $id): JsonResponse
    {
        $player = $this->playerService->find($id);

        if (!$player) {
            return response()->json([
                'message' => 'Player not found'
            ], 404);
        }

        return response()->json([
            'data' => $player
        ]);
    }

    /**
     * Delete player
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->playerService->delete($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Player not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Player deleted'
        ]);
    }
}