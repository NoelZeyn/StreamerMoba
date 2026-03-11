<?php

namespace App\DTO;

class CreateTransactionDTO
{
    public function __construct(
        public string $player_id,
        public int $quantity,
        public int $price
    ) {
        
    }
}