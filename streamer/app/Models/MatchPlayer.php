<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchPlayer extends Model
{
    protected $fillable = [
        'match_id',
        'player_id',
        'hero_id',
        'role_id'
    ];

    public function match()
    {
        return $this->belongsTo(Matchs::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function hero()
    {
        return $this->belongsTo(Hero::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}