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
            // Hanya ambil kolom yang dibutuhkan untuk list dashboard
            ->select(['id', 'user_id', 'title', 'start_time', 'status'])
            ->where('user_id', $userId)
            ->latest()
            // simplePaginate jauh lebih cepat untuk data besar dibanding paginate() atau get()
            ->simplePaginate($perPage);
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
}
