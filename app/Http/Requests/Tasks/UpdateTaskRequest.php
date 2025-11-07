<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,in-progress,completed',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required when updating.',
            'status.in' => 'Invalid status value. Allowed: pending, in-progress, completed.',
        ];
    }
}
