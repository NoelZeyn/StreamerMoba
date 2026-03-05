<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = [
        'name',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->hasOne(VipWallet::class);
    }
    
    public function matchPlayers(){
        return $this->hasOne(MatchPlayer::class);
    }
}
