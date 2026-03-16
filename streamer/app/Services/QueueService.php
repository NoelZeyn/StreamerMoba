<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Queue;
use App\Repositories\QueueRepository;
use App\Models\Schedule;
use Exception;
use Illuminate\Support\Facades\Auth;

class QueueService
{
    public function __construct(
        protected QueueRepository $queueRepository
    ) {}

    public function joinPublicQueue(array $data)
    {
        $schedule = Schedule::findOrFail($data['schedule_id']);

        $existing = $this->queueRepository->checkExistingInQueue($data['schedule_id'], $data['mlbb_id']);
        if ($existing) {
            throw new Exception("Kamu sudah ada dalam antrean aktif!");
        }

        $nextNumber = $this->queueRepository->getNextQueueNumber($data['schedule_id']) + 1;

        $payload = array_merge($data, [
            'user_id'      => $schedule->user_id,
            'queue_number' => $nextNumber,
            'status'       => 'pending'
        ]);

        return $this->queueRepository->create($payload);
    }



public function getQueueVIP()
{
    return Player::where('user_id', Auth::id())
        ->where('type', 'VIP')
        ->with('wallet')
        ->whereHas('wallet')
        ->get()
        ->sortBy('wallet.play_balance')
        ->values();
}
public function getQueuePublic($scheduleId)
{
    return Queue::where('schedule_id', $scheduleId)
        ->orderBy('queue_number', 'asc')
        ->get();
}

public function changeStatus($queueId, $status)
{
    return $this->queueRepository->updateStatus($queueId, $status);
}
}