<?php

namespace App\Services;

use App\Repositories\PlayerRepository;
use App\DTO\CreatePlayerDTO;
use Illuminate\Support\Facades\DB;

class PlayerService
{

    public function __construct(
        protected PlayerRepository $playerRepository
    ) {}

    public function create(CreatePlayerDTO $dto)
    {
        return DB::transaction(function () use ($dto) {

            $player = $this->playerRepository->create([
                'name' => $dto->name,
                'type' => $dto->type
            ]);

            if ($dto->type === 'VIP') {

                $this->playerRepository->createWallet($player->id, 0);

            }

            return $player;

        });
    }

    public function list()
    {
        return $this->playerRepository->getAll();
    }

}