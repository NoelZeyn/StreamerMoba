<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicQueueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mlbb_id'     => 'required|string|max:20',
            'mlbb_server' => 'required|string|max:10',
            'nickname'    => 'required|string|max:50',
            'schedule_id' => 'required|exists:stream_schedules,id',
        ];
    }
}