<?php

namespace App\DTO;

class CreateHeroDTO
{
    public function __construct(
        public string $name,
    ) {}
}