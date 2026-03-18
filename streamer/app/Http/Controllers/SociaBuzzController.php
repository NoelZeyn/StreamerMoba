<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SociaBuzzController extends Controller
{
    public function __construct(protected TransactionService $transactionService) {}

    public function handle(Request $request, $token): JsonResponse
    {
        $user = User::where('sociabuzz_token', $token)->first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Invalid Token'], 404);
        }

        $payload = $request->all();

        $data = [
            'donator_name' => $payload['supporter'] ?? 'Anonymous', // Di JSON kamu: "supporter":"Jessica"
            'amount'       => (int) ($payload['amount'] ?? 0),      // Di JSON kamu: "amount":10000
            'message'      => $payload['message'] ?? '',           // Di JSON kamu: "message":"..."
            'email'        => $payload['email_supporter'] ?? null, // Di JSON kamu: "email_supporter":"..."
            'external_id'  => $payload['id'] ?? null,              // Di JSON kamu: "id":"5597510802"
            'platform'     => 'sociabuzz',
            'raw_payload'  => $payload
        ];

        $this->transactionService->processIncomingDonation($user, $data);

        return response()->json(['status' => true]);
    }
}
