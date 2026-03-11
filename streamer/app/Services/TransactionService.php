<?php

namespace App\Services;

use App\DTO\CreateTransactionDTO;
use App\Repositories\MatchRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\PlayerRepository;
use App\Repositories\VipWalletRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class TransactionService
{

    public function __construct(
        protected TransactionRepository $transactionRepository,
        protected PlayerRepository $playerRepository,
        protected VipWalletRepository $walletRepository
    ) {}

    public function createTransaction(CreateTransactionDTO $dto)
    {
        if ($dto->quantity <= 0) {
            throw new Exception('Quantity must be greater than 0');
        }

        return DB::transaction(function () use ($dto) {
            $player = $this->playerRepository->find($dto->player_id);
            $wallet = $this->walletRepository->lockByPlayer($dto->player_id);

            if (!$player) {
                throw new Exception('Player not found');
            }

            if (!$wallet) {
                throw new Exception('Wallet not found');
            }

            $this->walletRepository->increment($wallet, $dto->quantity);

            $transaction = $this->transactionRepository->create([
                'user_id' => Auth::id(),
                'player_id' => $dto->player_id,
                'quantity'  => $dto->quantity,
                'price'     => $dto->price,
                'status'    => 'success'
            ]);

            return $transaction;
        });
    }

    public function list()
    {
        return $this->transactionRepository->all();
    }

    public function find(int $id)
    {
        return $this->transactionRepository->find($id);
    }

    public function delete(int $id)
    {
        try {
             DB::transaction(function () use ($id) {
                $transaction = $this->transactionRepository->findOrFail($id);
                $wallet = $this->walletRepository->lockByPlayer($transaction->player_id);

                if (!$wallet) {
                    throw new Exception('Wallet not found');
                }

                $this->walletRepository->decrement($wallet, $transaction->quantity);
                $this->transactionRepository->delete($transaction);
            });
        } catch (Exception $e) {
            throw new Exception('Failed to delete transaction: ' . $e->getMessage());
        }
    }
}
