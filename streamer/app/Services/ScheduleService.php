<?php

namespace App\Services;

use App\Repositories\ScheduleRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class ScheduleService
{

    public function __construct(
        protected ScheduleRepository $scheduleRepository,
    ) {}


    public function list()
    {
        return $this->scheduleRepository->listByUser(Auth::id());
    }

    public function finishSchedule(int $id)
    {
        return DB::transaction(function () use ($id) {
            $schedule = $this->scheduleRepository->findOrFail($id);

            if ($schedule->user_id !== Auth::id()) {
                throw new Exception("Unauthorized");
            }

            return $this->scheduleRepository->markAsFinished($schedule);
        });
    }

    public function cancelSchedule(int $id)
    {
        return DB::transaction(function () use ($id) {
            $schedule = $this->scheduleRepository->findOrFail($id);

            if ($schedule->user_id !== Auth::id()) {
                throw new Exception("Unauthorized");
            }

            return $this->scheduleRepository->markAsCancelled($schedule);
        });
    }

    public function startSchedule(int $id)
    {
        return DB::transaction(function () use ($id) {
            $schedule = $this->scheduleRepository->findOrFail($id);

            if ($schedule->user_id !== Auth::id()) {
                throw new Exception("Unauthorized");
            }

            return $this->scheduleRepository->markAsStarted($schedule);
        });
    }

    public function reopenSchedule(int $id)
    {
        return DB::transaction(function () use ($id) {
            $schedule = $this->scheduleRepository->findOrFail($id);

            if ($schedule->user_id !== Auth::id()) {
                throw new Exception("Unauthorized");
            }

            return $this->scheduleRepository->markAsReopened($schedule);
        });
    }

    public function createSchedule($dto)
    {
        return DB::transaction(function () use ($dto) {
                $schedule = $this->scheduleRepository->create([
                    'user_id' => Auth::id(),
                    'title' => $dto->title,
                    'start_time' => $dto->start_time,
                    'end_time' => $dto->end_time,
                    'status' => $dto->status,
                    'notes' => $dto->notes
                ]);
                return $schedule;
        });
    }

    public function updateSchedule($id, $dto)
    {
        return DB::transaction(function () use ($id, $dto) {
                $schedule = $this->scheduleRepository->findOrFail($id);

                if ($schedule->user_id !== Auth::id()) {
                    throw new Exception("Unauthorized");
                }

                $schedule = $this->scheduleRepository->update($schedule, [
                    'title' => $dto->title,
                    'start_time' => $dto->start_time,
                    'end_time' => $dto->end_time,
                    'status' => $dto->status,
                    'notes' => $dto->notes
                ]);

                return $schedule;
        });
    }

    public function deleteSchedule($id)
    {
        return DB::transaction(function () use ($id) {
                $schedule = $this->scheduleRepository->findOrFail($id);

                if ($schedule->user_id !== Auth::id()) {
                    throw new Exception("Unauthorized");
                }

                $this->scheduleRepository->delete($schedule);
                return true;
        });
    }

    public function getSchedule($id)
    {
        $schedule = $this->scheduleRepository->findWithMatches($id);

        if ($schedule->user_id !== Auth::id()) {
            throw new Exception("Unauthorized");
        }

        return $schedule;
    }
}