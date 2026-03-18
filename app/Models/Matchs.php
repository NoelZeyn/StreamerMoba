<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matchs extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'user_id',
        'season_id',
        'schedule_id',
        'played_at'
    ];

    public function players()
    {
        return $this->hasMany(MatchPlayer::class, 'match_id');
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}