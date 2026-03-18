<?php

namespace App\DTO;

class MatchPlayerDTO
{
    public function __construct(
        public int $player_id,
        public int $hero_id,
        public int $role_id
    ) {}
}