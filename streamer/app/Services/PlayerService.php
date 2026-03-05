<?php

namespace App\Services;

use App\Repositories\PlayerRepository;
use App\Repositories\VipWalletRepository;
use App\DTO\CreatePlayerDTO;
use Illuminate\Support\Facades\DB;

class PlayerService
{

    public function __construct(
        protected PlayerRepository $playerRepository,
        protected VipWalletRepository $walletRepository
    ) {}

    public function create(CreatePlayerDTO $dto)
    {
        return DB::transaction(function () use ($dto) {

            $player = $this->playerRepository->create([
                'name' => $dto->name,
                'type' => $dto->type
            ]);

            if ($dto->type === 'VIP') {
                $this->walletRepository->create($player->id);
            }

            return $player->load('wallet');
        });
    }

    public function list()
    {
        return $this->playerRepository->all();
    }

    public function find(int $id)
    {
        return $this->playerRepository->find($id);
    }

    public function update(int $id, array $data)
    {
        $player = $this->playerRepository->findOrFail($id);

        return $this->playerRepository->update($player, $data);
    }

    public function delete(int $id)
    {
        $player = $this->playerRepository->findOrFail($id);

        return $this->playerRepository->delete($player);
    }

    public function addBalance(int $playerId, int $amount)
    {
        return DB::transaction(function () use ($playerId, $amount) {

            $wallet = $this->walletRepository->lockByPlayer($playerId);

            if (!$wallet) {
                throw new \Exception("Wallet not found");
            }

            return $this->walletRepository->increment($wallet, $amount);
        });
    }

    public function reduceBalance(int $playerId, int $amount)
    {
        return DB::transaction(function () use ($playerId, $amount) {

            $wallet = $this->walletRepository->lockByPlayer($playerId);

            if (!$wallet || $wallet->play_balance < $amount) {
                throw new \Exception("Insufficient balance");
            }

            return $this->walletRepository->decrement($wallet, $amount);
        });
    }
}