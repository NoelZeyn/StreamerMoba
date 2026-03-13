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
            'user_id' => 'required|exists:users,id',
            'season_id' => 'required|exists:seasons,id',
            'schedule_id' => 'required|exists:schedules,id',
            'players' => 'required|array|min:1',
            'players.*.player_id' => 'required|exists:players,id',
            'players.*.hero_id' => 'required|exists:heroes,id',
            'players.*.role_id' => 'required|exists:roles,id'
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
