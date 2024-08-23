<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:today',
            'task_priority' => 'nullable|in:Urgent,High,Normal,Low',
        ];
    }

    public function messages()
    {
        // there are some custom error messages (it's optional)
        return [
            'title.required' => 'The title is required.',
            'description.required' => 'The description is required.',
            'due_date.date_format' => 'The due date must be in the format YYYY-MM-DD.',
            'due_date.after_or_equal' => 'The due date cannot be earlier than today.',
            'priority.in' => 'The priority must be either Urgent, High, Normal, or Low.',
        ];
    }
}
