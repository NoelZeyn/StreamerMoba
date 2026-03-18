<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'type' => 'required|in:VIP,PUBLIC',
            'play_balance' => 'nullable|integer|min:0',
            'mlbb_id' => 'nullable|string|max:20',
            'mlbb_server' => 'nullable|string|max:20'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Player name is required',
            'name.string' => 'Player name must be a string',
            'name.max' => 'Player name cannot exceed 100 characters',
            'type.required' => 'Player type is required',
            'type.in' => 'Player type must be either VIP or PUBLIC',
            'mlbb_id.string' => 'MLBB ID must be a string',
            'mlbb_id.max' => 'MLBB ID cannot exceed 20 characters',
            'mlbb_server.string' => 'MLBB Server must be a string',
            'mlbb_server.max' => 'MLBB Server cannot exceed 20 characters'

        ];
    }
}