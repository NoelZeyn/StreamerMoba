<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'player_id' => 'required|exists:players,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|integer|min:0',
        ];
    }
    public function messages()
    {
        return [
            'user_id.required' => 'User is required',
            'user_id.exists' => 'Selected user is not available',
            'player_id.required' => 'Player is required',
            'player_id.exists' => 'Selected player is not available',
            'quantity.required' => 'Quantity is required',
            'quantity.integer' => 'Quantity must be an integer',
            'quantity.min' => 'Quantity must be at least 1',
            'price.required' => 'Price is required',
            'price.integer' => 'Price must be an integer',
            'price.min' => 'Price must be at least 0',
        ];
    }
}
