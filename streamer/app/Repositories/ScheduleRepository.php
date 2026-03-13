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
        return Schedule::with('matches.players')->findOrFail($scheduleId);
    }

    public function listByUser(int $userId)
    {
        return Schedule::with('matches.players')
            ->where('user_id', $userId)
            ->latest()
            ->get();
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