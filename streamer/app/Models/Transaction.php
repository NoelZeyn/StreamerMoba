<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'player_id',
        'plays_added',
        'price'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}