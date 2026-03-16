<?php

namespace App\Repositories;

use App\Models\Donation;

class DonationRepository
{
    public function getAllByUserId($userId)
    {
        return Donation::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data)
    {
        return Donation::create($data);
    }

    public function findBySaweriaId($saweriaId)
    {
        return Donation::where('saweria_id', $saweriaId)->first();
    }
}