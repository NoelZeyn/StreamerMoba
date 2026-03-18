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
        $user = User::where('webhook_token', $token)->first();
        if (!$user) return response()->json(['status' => false], 404);

        $validated = $request->validated();

        $data = [
            'donator_name' => $validated['donator_name'] ?? 'Anonymous',
            'amount'       => $validated['amount_raw'] ?? 0,
            'message'      => $validated['message'] ?? '',
            'email'        => $validated['donator_email'] ?? null,
            'external_id'  => $validated['id'] ?? null,
            'platform'     => 'saweria',
            'raw_payload'  => $request->all()
        ];

        $this->transactionService->processIncomingDonation($user, $data);

        return response()->json(['status' => true]);
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
