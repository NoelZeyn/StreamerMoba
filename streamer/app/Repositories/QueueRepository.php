<?php

namespace App\Repositories;

use App\Models\Queue;

class QueueRepository
{
    public function checkExistingInQueue($scheduleId, $mlbbId)
    {
        return Queue::where('schedule_id', $scheduleId)
            ->where('mlbb_id', $mlbbId)
            ->whereIn('status', ['pending', 'playing'])
            ->first();
    }

    public function getNextQueueNumber($scheduleId)
    {
        return Queue::where('schedule_id', $scheduleId)->max('queue_number') ?? 0;
    }

    public function create(array $data)
    {
        return Queue::create($data);
    }



    public function updateStatus($queueId, $status)
{
    $queue = Queue::findOrFail($queueId);
    $queue->update(['status' => $status]);
    return $queue;
}
}