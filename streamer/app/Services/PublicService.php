<?php

namespace App\Services;

use App\Repositories\PublicRepository;

class PublicService
{
    public function __construct(
        protected PublicRepository $publicRepo
    ) {}

    public function getStreamerList()
    {
        return $this->publicRepo->getAllStreamers();
    }

    public function getSchedulesForStreamer($userId)
    {
        return $this->publicRepo->getLiveSchedulesByStreamer($userId);
    }
    
    public function getQueueList($scheduleId)
    {
        return $this->publicRepo->getQueueBySchedule($scheduleId);
    }
}
