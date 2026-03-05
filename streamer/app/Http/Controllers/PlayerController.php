<?php

namespace App\Http\Controllers;

use App\DTO\CreatePlayerDTO;
use App\Http\Requests\CreatePlayerRequest;
use App\Services\PlayerService;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function __construct(
        protected PlayerService $playerService
    ) {} 
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $players = $this->playerService->list();

        return response()->json([
            'data' => $players
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePlayerRequest $request)
    {

        $dto = new CreatePlayerDTO(
            $request->name,
            $request->type
        );

        $player = $this->playerService->create($dto);

        return response()->json([
            'message' => 'Player created',
            'data' => $player
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
