<?php

namespace App\Http\Controllers;

use App\DTO\CreateScheduleDTO;
use App\Http\Requests\CreateScheduleRequest;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ScheduleController extends Controller
{
    public function __construct(
        protected ScheduleService $scheduleService
    ) {}

    public function index()
    {
        try {
            $schedules = $this->scheduleService->list();
            return $this->success($schedules, 'Schedules retrieved successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateScheduleRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $dto = new CreateScheduleDTO(
                Auth::id(),
                $validated['title'],
                $validated['start_time'],
                $validated['end_time'] ?? null,
                $validated['status'],
                $validated['notes'] ?? null
            );
            $schedule = $this->scheduleService->createSchedule($dto);
            return $this->success($schedule, 'Schedule created successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $schedule = $this->scheduleService->getSchedule($id);
            if (!$schedule) {
                return $this->error('Schedule not found', 404);
            }
            return $this->success($schedule, 'Schedule retrieved successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( int $id, CreateScheduleRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $dto = new CreateScheduleDTO(
                Auth::id(),
                $validated['title'],
                $validated['start_time'],
                $validated['end_time'] ?? null,
                $validated['status'],
                $validated['notes'] ?? null
            );

            $schedule = $this->scheduleService->updateSchedule($id, $dto);

            return $this->success($schedule, 'Schedule updated successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $this->scheduleService->deleteSchedule($id);
            return $this->success(null, 'Schedule deleted successfully');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function finish(int $id)
    {
        try {
            $this->scheduleService->finishSchedule($id);
            return $this->success(null, 'Schedule marked as finished');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function cancel(int $id)
    {
        try {
            $this->scheduleService->cancelSchedule($id);
            return $this->success(null, 'Schedule marked as cancelled');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function start(int $id)
    {
        try {
            $this->scheduleService->startSchedule($id);
            return $this->success(null, 'Schedule marked as started');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function reopen(int $id)
    {
        try {
            $this->scheduleService->reopenSchedule($id);
            return $this->success(null, 'Schedule marked as reopened');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }
}
