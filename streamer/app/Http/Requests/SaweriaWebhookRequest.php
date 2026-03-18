<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaweriaWebhookRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'donator_name' => 'nullable|string',
            'amount_raw'   => 'required|numeric',
            'message'      => 'nullable|string',
            'id'           => 'required|string',
            'email'        => 'nullable|email',
        ];
    }
}