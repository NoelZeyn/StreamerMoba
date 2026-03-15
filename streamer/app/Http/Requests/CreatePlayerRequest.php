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
            'play_balance' => 'nullable|integer|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Player name is required',
            'name.string' => 'Player name must be a string',
            'name.max' => 'Player name cannot exceed 100 characters',
            'type.required' => 'Player type is required',
            'type.in' => 'Player type must be either VIP or PUBLIC'
        ];
    }
}