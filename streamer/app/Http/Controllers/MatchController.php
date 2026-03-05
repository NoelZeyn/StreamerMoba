<?php

namespace App\Http\Controllers;

use App\DTO\CreateMatchDTO;
use App\DTO\MatchPlayerDTO;
use App\Http\Requests\CreateMatchRequest;
use App\Services\MatchService;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function __construct(
        protected MatchService $matchService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateMatchRequest $request)
    {

        $players = collect($request->players)
            ->map(fn ($player) => new MatchPlayerDTO(
                $player['player_id'],
                $player['hero_id'],
                $player['role_id']
            ))
            ->toArray();

        $dto = new CreateMatchDTO(
            $request->season_id,
            $players
        );

        $match = $this->matchService->createMatch($dto);

        return response()->json([
            'message' => 'Match created',
            'data' => $match
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
