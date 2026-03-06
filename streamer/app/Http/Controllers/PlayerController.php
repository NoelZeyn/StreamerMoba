<?php

namespace App\Http\Controllers;

use App\DTO\CreatePlayerDTO;
use App\Http\Requests\CreatePlayerRequest;
use App\Models\Player;
use App\Services\PlayerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Exception;

class PlayerController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected PlayerService $playerService
    ) {}

    /**
     * List all players
     */
    public function index(): JsonResponse
    {
        try {
            $this->authorize('view', Player::class);
            $players = $this->playerService->list();
            return $this->success($players, 'Players retrieved successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
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
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Show player
     */
    public function show(int $id): JsonResponse
    {
        try {
            $player = $this->playerService->find($id);

            if (!$player) {
                return response()->json([
                    'message' => 'Player not found'
                ], 404);
            }

            $this->authorize('view', $player);

            return $this->success($player, 'Player retrieved successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
    

    /**
     * Delete player
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $player = $this->playerService->find($id);
            $this->authorize('delete', $player);
            $this->playerService->delete($id);
            return $this->success(null, 'Player deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
