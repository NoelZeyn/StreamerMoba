<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMatchRequest extends FormRequest
{
    public function rules()
    {
        return [

            'season_id' => 'required|exists:seasons,id',
            'players' => 'required|array|min:1|max:5',
            'players.*.player_id' => [
                'required',
                'exists:players,id',
                'distinct'
            ],
            'players.*.hero_id' => 'required|exists:heroes,id',
            'players.*.role_id' => 'required|exists:roles,id',

        ];
    }
}