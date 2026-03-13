<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Models\MatchPlayer;

class ScheduleRepository
{
    public function create(array $data): Schedule
    {
        return Schedule::create($data);
    }

    public function findById(int $scheduleId): ?Schedule
    {
        return Schedule::find($scheduleId);
    }

    public function findWithMatches(int $scheduleId): Schedule
    {
        return $schedule = Schedule::with([
            'matches.user:id,name',
            'matches.players.player:id,name', // Schedule -> Matches -> MatchPlayer -> Player
            'matches.players.hero:id,name',   // Jika ingin ambil nama Hero juga
            'matches.players.role:id,name'    // Jika ingin ambil nama Role juga
        ])->findOrFail($scheduleId);
    }

public function listByUser(int $userId, int $perPage = 10)
{
    return Schedule::query()
        ->select([
            'id',
            'user_id',
            'title',
            'start_time',
            'status'
        ])
        ->where('user_id', $userId)
        ->orderByDesc('start_time')
        ->paginate($perPage);
}

    public function update(Schedule $schedule, array $data): Schedule
    {
        $schedule->update($data);
        return $schedule;
    }

    public function delete(Schedule $schedule): bool
    {
        return $schedule->delete();
    }

    public function findOrFail(int $scheduleId): Schedule
    {
        return Schedule::findOrFail($scheduleId);
    }

    public function markAsFinished(Schedule $schedule): Schedule
    {
        $schedule->status = 'finished';
        $schedule->end_time = now();
        $schedule->save();
        return $schedule;
    }

    public function markAsCancelled(Schedule $schedule): Schedule
    {
        $schedule->status = 'cancelled';
        $schedule->end_time = now();
        $schedule->save();
        return $schedule;
    }

    public function markAsStarted(Schedule $schedule): Schedule
    {
        $schedule->status = 'live';
        $schedule->save();
        return $schedule;
    }

    public function markAsReopened(Schedule $schedule): Schedule
    {
        $schedule->status = 'scheduled';
        $schedule->end_time = null;
        $schedule->save();

        return $schedule;
    }
}
