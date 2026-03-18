<?php

namespace App\Services;

use App\Repositories\DonationRepository;
use App\Models\Player;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DonationService
{
    public function __construct(
        protected DonationRepository $donationRepo
    ) {}

    public function getDonationHistory($userId)
    {
        return $this->donationRepo->getAllByUserId($userId);
    }

    public function findOrCreatePlayer($userId, $mlbbId, $mlbbServer)
    {
        $player = Player::where('user_id', $userId)
            ->where('mlbb_id', $mlbbId)
            ->where('mlbb_server', $mlbbServer)
            ->first();

        if ($player) return $player;

        try {
            $response = Http::timeout(10)->withoutVerifying()
                ->get("https://api.isan.eu.org/nickname/ml", [
                    'id' => $mlbbId,
                    'zone' => $mlbbServer
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['success']) && $data['success']) {
                    $nickname = urldecode(str_replace('+', ' ', $data['name']));
                    
                    return Player::create([
                        'user_id' => $userId,
                        'name' => $nickname,
                        'mlbb_id' => $mlbbId,
                        'mlbb_server' => $mlbbServer,
                        'type' => 'VIP',
                        'play_balance' => 0
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("DonationService AutoCreate Error: " . $e->getMessage());
        }

        return null;
    }
}