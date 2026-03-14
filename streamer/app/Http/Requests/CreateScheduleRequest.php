<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules()
    {
        return [
            // 'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'status' => 'required|in:scheduled,completed,cancelled',
            'notes' => 'nullable|string'
        ];
    }
    public function messages()
    {
        return [
            'user_id.required' => 'User ID is required',
            'user_id.exists' => 'User ID must exist in the users table',
            'title.required' => 'Title is required',
            'title.string' => 'Title must be a string',
            'title.max' => 'Title must not exceed 255 characters',
            'start_time.required' => 'Start time is required',
            'start_time.date' => 'Start time must be a valid date',
            'end_time.required' => 'End time is required',
            'end_time.date' => 'End time must be a valid date',
            'end_time.after' => 'End time must be after start time',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be one of: scheduled, completed, cancelled',
            'notes.string' => 'Notes must be a string'
        ];
    }
}
