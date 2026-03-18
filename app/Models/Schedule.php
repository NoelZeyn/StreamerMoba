<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'stream_schedules';

    protected $fillable = [
        'user_id',
        'title',
        'start_time',
        'end_time',
        'status',
        'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function matches()
    {
        return $this->hasMany(Matchs::class, 'schedule_id');
    }
}
