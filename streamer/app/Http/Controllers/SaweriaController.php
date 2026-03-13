<?php
namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;

class SaweriaController extends Controller
{
    public function handle(Request $request, $token)
    {
        $user = User::where('webhook_token', $token)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid webhook token'
            ], 404);
        }

        $data = $request->all();

        Donation::create([
            'user_id' => $user->id,
            'donator_name' => $data['donator_name'] ?? 'Anonymous',
            'email' => $data['email'] ?? null,
            'message' => $data['message'] ?? null,
            'amount' => $data['amount_raw'] ?? 0,
            'currency' => 'IDR',
            'saweria_id' => $data['id'] ?? null,
            'donated_at' => now(),
            'raw_payload' => $data
        ]);

        return response()->json([
            'status' => true
        ]);
    }
}