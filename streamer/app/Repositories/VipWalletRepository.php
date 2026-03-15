<?php

namespace App\Repositories;

use App\Models\VipWallet;

class VipWalletRepository
{

    public function create(int $playerId, int $balance): VipWallet
    {
        return VipWallet::create([
            'player_id' => $playerId,
            'play_balance' => $balance
        ]);
    }

    public function findByPlayer(int $playerId): ?VipWallet
    {
        return VipWallet::where('player_id', $playerId)->first();
    }

    public function lockByPlayer(int $playerId): ?VipWallet
    {
        return VipWallet::where('player_id', $playerId)
            ->lockForUpdate()
            ->first();
    }

    public function increment(VipWallet $wallet, int $amount): VipWallet
    {
        $wallet->increment('play_balance', $amount);
        return $wallet->refresh();
    }

    public function decrement(VipWallet $wallet, int $amount): VipWallet
    {
        $wallet->decrement('play_balance', $amount);
        return $wallet->refresh();
    }
}