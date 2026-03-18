<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
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
            'name.required' => 'Role name is required',
            'name.string' => 'Role name must be a string',
            'name.max' => 'Role name must not exceed 255 characters',
        ];
    }
}
