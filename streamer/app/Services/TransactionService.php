<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\Player;
use App\DTO\CreateTransactionDTO;
use App\DTO\CreatePlayerDTO;
use App\Repositories\PlayerRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\VipWalletRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TransactionService
{
    public function __construct(
        protected TransactionRepository $transactionRepository,
        protected PlayerRepository $playerRepository,
        protected VipWalletRepository $walletRepository,
        protected PlayerService $playerService
    ) {}

    /**
     * Logika utama untuk memproses semua jenis donasi masuk
     */
    public function processIncomingDonation($user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            // 1. Simpan ke Tabel Donations
            Donation::create([
                'user_id'      => $user->id,
                'donator_name' => $data['donator_name'],
                'email'        => $data['email'] ?? null,
                'message'      => $data['message'] ?? '',
                'amount'       => $data['amount'],
                'platform'     => $data['platform'],
                'external_id'  => $data['external_id'],
                'raw_payload'  => $data['raw_payload'],
                'donated_at'   => now(),
            ]);

            $message = $data['message'];
            $amount  = $data['amount'];

            // 2. Cek Keyword VIP
            if (str_contains(strtoupper($message), 'VIP')) {
                $pattern = '/(\d{8,12})\s*\((\d{4,6})\)/';

                if (preg_match($pattern, $message, $matches)) {
                    $mlbbId = $matches[1];
                    $mlbbServer = $matches[2];

                    $player = Player::where('user_id', $user->id)
                        ->where('mlbb_id', $mlbbId)
                        ->where('mlbb_server', $mlbbServer)
                        ->first();

                    if (!$player) {
                        $player = $this->autoCreatePlayer($user->id, $mlbbId, $mlbbServer);
                    }

                    if ($player) {
                        $qty = floor($amount / 1000);
                        $this->executeVipTopup($player, $qty, $amount, $user->id);
                    }
                } else {
                    // Jika format (zone) salah, coba cari via nickname donatur
                    $this->processByNickname($data['donator_name'], $amount, $user->id);
                }
            }
        });
    }

    protected function executeVipTopup($player, $qty, $amount, $userId)
    {
        $wallet = $this->walletRepository->lockByPlayer($player->id);
        if (!$wallet) return;

        $this->walletRepository->increment($wallet, $qty);

        return $this->transactionRepository->create([
            'user_id'   => $userId,
            'player_id' => $player->id,
            'quantity'  => $qty,
            'price'     => $amount,
            'status'    => 'success'
        ]);
    }

    private function autoCreatePlayer($userId, $mlbbId, $mlbbServer)
    {
        try {
            $response = Http::retry(5, 200, function ($exception) {
                return $exception instanceof \Illuminate\Http\Client\ConnectionException ||
                    str_contains(strtolower($exception->getMessage()), 'ssl');
            })
                ->timeout(60)
                ->withoutVerifying()
                ->get("https://api.isan.eu.org/nickname/ml", [
                    'id'   => $mlbbId,
                    'zone' => $mlbbServer
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['success']) && $data['success'] === true) {
                    $nickname = urldecode(str_replace('+', ' ', $data['name']));

                    return $this->playerService->create(new CreatePlayerDTO(
                        user_id: $userId,
                        name: $nickname,
                        type: 'VIP',
                        mlbb_id: $mlbbId,
                        mlbb_server: $mlbbServer,
                        play_balance: 0
                    ));
                }

                if (isset($data['success']) && $data['success'] === false) {
                    Log::warning("AutoCreate Player: ID/Server tidak ditemukan", [
                        'user_id' => $userId,
                        'mlbb_id' => $mlbbId,
                        'zone'    => $mlbbServer,
                        'message' => $data['message'] ?? 'Not found'
                    ]);
                    return null;
                }
            }
        } catch (\Exception $e) {
            Log::error("AutoCreate Player Fatal Error: " . $e->getMessage(), [
                'mlbb_id' => $mlbbId,
                'zone'    => $mlbbServer
            ]);
        }

        return null;
    }

    private function processByNickname($name, $amount, $userId)
    {
        $player = Player::where('user_id', $userId)->where('name', $name)->where('type', 'VIP')->first();
        if ($player) {
            $this->executeVipTopup($player, floor($amount / 1000), $amount, $userId);
        }
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
