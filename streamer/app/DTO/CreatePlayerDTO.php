<?php

namespace App\DTO;

class CreatePlayerDTO
{
    public function __construct(
        public ?int $user_id,
        public string $name,
        public string $type
    ) {
        
    }
}