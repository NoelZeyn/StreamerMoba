<?php

namespace App\Repositories;

use App\Models\Queue;
use App\Models\User;
use App\Models\Schedule;

class PublicRepository
{
    public function getAllStreamers()
    {
        return User::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getLiveSchedulesByStreamer($userId)
    {
        return Schedule::select('id', 'title', 'status')
            ->where('user_id', $userId)
            ->where('status', 'LIVE')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getQueueBySchedule($scheduleId)
    {
        return Queue::where('schedule_id', $scheduleId)
            ->orderBy('queue_number', 'asc')
            ->get();
    }


    public function searchVipByName($name, $streamerId)
{
    return User::role('VIP') 
        ->where('name', 'LIKE', "%{$name}%")
        ->with(['vipWallet' => function($q) use ($streamerId) {
            $q->where('user_id', $streamerId); 
        }])
        ->get();
}
}
