<?php

namespace App\DTO;

class CreateMatchDTO
{
    public function __construct(
        public int $season_id,
        /** @var MatchPlayerDTO[] */
        public array $players
    ) {}
}