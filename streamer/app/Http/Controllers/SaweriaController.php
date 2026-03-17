<?php

namespace App\Http\Controllers;

use App\DTO\CreatePlayerDTO;
use App\Models\User;
use App\Models\Donation;
use App\Models\Player;
use App\Services\TransactionService;
use App\DTO\CreateTransactionDTO;
use App\Http\Requests\SaweriaWebhookRequest;
use App\Services\PlayerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SaweriaController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService,
        protected PlayerService $playerService
    ) {}

    public function handle(SaweriaWebhookRequest $request, $token): JsonResponse
    {
        try {
            $validated = $request->validated();
            $user = User::where('webhook_token', $token)->first();
    
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Invalid Token'], 404);
            }

            return DB::transaction(function () use ($user, $validated, $request) {
                $donatorName = $validated['donator_name'] ?? 'Anonymous';
                $amount      = $validated['amount_raw'] ?? 0;
                $message     = $validated['message'] ?? '';

                Donation::create([
                    'user_id'      => $user->id,
                    'donator_name' => $donatorName,
                    'email'        => $validated['email'] ?? null,
                    'message'      => $message,
                    'amount'       => $amount,
                    'saweria_id'   => $validated['id'] ?? null,
                    'raw_payload'  => $request->all(),
                    'donated_at'   => now(),
                ]);

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
                        // Jika Player ada (lama) atau berhasil dibuat (baru)
                        if ($player) {
                            $qty = floor($amount / 1000);
                            $dto = new CreateTransactionDTO(
                                player_id: $player->id,
                                quantity: $qty,
                                price: $amount
                            );

                            $this->transactionService->createTransactionFromWebhook($dto, $user->id);
                        } else {
                            Log::error("Saweria: Gagal memproses transaksi karena Player tidak ditemukan & gagal auto-create API untuk ID $mlbbId.");
                        }
                    } else {
                        // Jika ada kata VIP tapi format ID salah, coba cari berdasarkan Nickname Donatur
                        Log::info("Saweria: Format ID tidak ditemukan dalam pesan, mencoba via Nickname: $donatorName");
                        $this->processByNickname($donatorName, $amount, $user->id);
                    }
                }

                return response()->json(['status' => true]);
            });
        } catch (\Throwable $th) {
            Log::error('Saweria Webhook Error: ' . $th->getMessage() . ' Line: ' . $th->getLine());
            return response()->json(['status' => false, 'error' => $th->getMessage()], 500);
        }
    }

    private function autoCreatePlayer($userId, $mlbbId, $mlbbServer)
    {
        try {
            // Timeout dalam detik
            $response = Http::timeout(5000)->withoutVerifying()->get("https://api.isan.eu.org/nickname/ml", [
                'id' => $mlbbId,
                'zone' => $mlbbServer
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['success']) && $data['success']) {
                    $nickname = urldecode(str_replace('+', ' ', $data['name']));

                    $dto = new CreatePlayerDTO(
                        user_id: $userId,
                        name: $nickname,
                        type: 'VIP',
                        mlbb_id: $mlbbId,
                        mlbb_server: $mlbbServer,
                        play_balance: 0
                    );
                    
                    return $this->playerService->create($dto);
                }
            }
            return null;
        } catch (\Exception $e) {
            Log::error("AutoCreatePlayer Service Error: " . $e->getMessage());
            return null;
        }
    }

    private function processByNickname($name, $amount, $userId)
    {
        $player = Player::where('user_id', $userId)
            ->where('name', $name)
            ->where('type', 'VIP')
            ->first();

        if ($player) {
            $dto = new CreateTransactionDTO($player->id, floor($amount / 1000), $amount);
            $this->transactionService->createTransactionFromWebhook($dto, $userId);
        }
    }

    /**
     * Proxy untuk Frontend Vue Nickname Checker
     */
    public function proxyNickname(Request $request): JsonResponse
    {
        $id = $request->query('id');
        $zone = $request->query('zone');

        if (!$id || !$zone) {
            return response()->json(['success' => false, 'message' => 'ID dan Zone wajib diisi'], 400);
        }

        try {
            $response = Http::timeout(5000)
                ->withoutVerifying()
                ->get("https://api.isan.eu.org/nickname/ml", [
                    'id' => $id,
                    'zone' => $zone
                ]);

            if ($response->failed()) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
            }

            $data = $response->json();
            if (isset($data['name'])) {
                $data['name'] = urldecode(str_replace('+', ' ', $data['name']));
            }

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error("MLBB Proxy Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server MLBB sibuk'], 500);
        }
    }
}
