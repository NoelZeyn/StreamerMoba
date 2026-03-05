<?php

namespace App\DTO;

class CreatePlayerDTO
{
    public function __construct(
        public string $name,
        public string $type
    ) {
        
    }
}