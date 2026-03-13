<?php

namespace App\DTO;

class CreateScheduleDTO
{
    public function __construct(
        public int $user_id,
        public string $title,
        public \DateTime $start_time,
        public \DateTime $end_time,
        public string $status,
        public ?string $notes = null
    ) {}
}