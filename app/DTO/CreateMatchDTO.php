<?php

namespace App\DTO;

class CreateMatchDTO
{
    public function __construct(
        public int $user_id,
        public int $season_id,
        public int $schedule_id,
        public array $players
    ) {}
}