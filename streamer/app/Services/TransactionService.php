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
use Illuminate\Support\Facades\Log;

class TransactionService
{

    public function __construct(
        protected TransactionRepository $transactionRepository,
        protected PlayerRepository $playerRepository,
        protected VipWalletRepository $walletRepository
    ) {}

    public function createTransactionFromWebhook(CreateTransactionDTO $dto, int $userId)
    {
        if ($dto->quantity <= 0) return;

        Log::info("Saweria: Membuat transaksi untuk Player ID {$dto->player_id} dengan quantity {$dto->quantity} dan price {$dto->price}");
        return DB::transaction(function () use ($dto, $userId) {
            $wallet = $this->walletRepository->lockByPlayer($dto->player_id);
            if (!$wallet) return null;

            $this->walletRepository->increment($wallet, $dto->quantity);

            return $this->transactionRepository->create([
                'user_id' => $userId,
                'player_id' => $dto->player_id,
                'quantity'  => $dto->quantity,
                'price'     => $dto->price,
                'status'    => 'success'
            ]);
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
