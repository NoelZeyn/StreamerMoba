<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VipWallet extends Model
{
    protected $fillable = [
        'player_id',
        'play_balance'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}