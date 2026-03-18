<?php

namespace App\DTO;

class CreateSeasonDTO
{
    public function __construct(
        public string $name,
        public string $start_date,
        public ?string $end_date = null,
        public bool $is_active = false
    ) {}
}