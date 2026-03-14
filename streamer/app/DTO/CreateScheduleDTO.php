<?php

namespace App\DTO;

class CreateScheduleDTO
{
    public function __construct(
        public int $user_id,
        public string $title,
        public string $start_time,
        public ?string $end_time,
        public string $status,
        public ?string $notes = null
    ) {}
}