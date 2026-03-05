<?php

namespace App\Services;

use App\Repositories\MatchRepository;
use App\Repositories\PlayerRepository;
use App\Repositories\VipWalletRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class MatchService
{

    public function __construct(
        protected MatchRepository $matchRepository,
        protected PlayerRepository $playerRepository,
        protected VipWalletRepository $walletRepository
    ) {}

    public function createMatch($dto)
    {
        return DB::transaction(function () use ($dto) {

            $match = $this->matchRepository->create([
                'user_id' => Auth::id(),
                'season_id' => $dto->season_id,
                'played_at' => now()
            ]);

            $playersData = [];

            foreach ($dto->players as $playerDTO) {

                $player = $this->playerRepository->findOrFail($playerDTO->player_id);

                if ($player->type === 'VIP') {

                    $wallet = $this->walletRepository
                        ->lockByPlayer($player->id);

                    if (!$wallet || $wallet->play_balance <= 0) {
                        throw new Exception("VIP balance empty");
                    }

                    $this->walletRepository->decrement($wallet, 1);
                }

                $playersData[] = [
                    'match_id' => $match->id,
                    'player_id' => $player->id,
                    'hero_id' => $playerDTO->hero_id,
                    'role_id' => $playerDTO->role_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            $this->matchRepository->insertPlayers($playersData);

            return $this->matchRepository->findWithPlayers($match->id);
        });
    }
}