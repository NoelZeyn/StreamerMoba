<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateHeroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Hero name is required',
            'name.string' => 'Hero name must be a string',
            'name.max' => 'Hero name must not exceed 255 characters',
        ];
    }
}
