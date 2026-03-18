<?php

namespace App\DTO;

class CreateRoleDTO
{
    public function __construct(
        public string $name,
    ) {}
}