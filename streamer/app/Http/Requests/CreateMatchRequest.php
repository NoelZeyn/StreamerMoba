<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules()
    {
        return [
            'season_id' => 'required|exists:seasons,id',
            'players' => 'required|array|min:1|max:10',
            'players.*.player_id' => [
                'required',
                'exists:players,id',
                'distinct'
            ],
            'players.*.hero_id' => 'required|exists:heroes,id',
            'players.*.role_id' => 'required|exists:roles,id',

        ];
    }

    public function messages()
    {
        return [
            'players.*.player_id.required' => 'Player is required for each player entry',
            'players.*.player_id.distinct' => 'Each player must be unique in the match',
            'players.*.player_id.exists' => 'Selected player is not available',
            'players.season_id.required' => 'Season is required',
            'players.season_id.exists' => 'Selected season is not available',
            'players.*.hero_id.required' => 'Hero is required for each player',
            'players.*.hero_id.exists' => 'Selected hero is not available',
            'players.*.role_id.required' => 'Role is required for each player',
            'players.*.role_id.exists' => 'Selected role is not available',
        ];
    }
}
