<?php

namespace App\Services;

use App\Repositories\MatchRepository;
use App\Models\Player;
use App\Models\VipWallet;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class MatchService
{
    public function __construct(
        protected MatchRepository $matchRepository
    ) {}

    public function createMatch($dto)
    {
        return DB::transaction(function () use ($dto) {

            $match = $this->matchRepository->createMatch([
                'user_id' => Auth::id(),
                'season_id' => $dto->season_id,
                'played_at' => now()
            ]);

            $playersData = [];

            foreach ($dto->players as $playerDTO) {

                $player = Player::findOrFail($playerDTO->player_id);

                if ($player->type === 'VIP') {

                    $wallet = VipWallet::where('player_id', $player->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$wallet || $wallet->play_balance <= 0) {
                        throw new Exception("VIP play balance empty");
                    }

                    $wallet->decrement('play_balance');
                }


                $playersData[] = [
                    'match_id' => $match->id,
                    'player_id' => $playerDTO->player_id,
                    'hero_id' => $playerDTO->hero_id,
                    'role_id' => $playerDTO->role_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            $this->matchRepository->insertPlayers($playersData);

            return $match->load([
                'players.hero',
                'players.role',
                'players.player'
            ]);
        });
    }
}
