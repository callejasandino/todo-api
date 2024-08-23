<?php

namespace App\Http\Requests\Update;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MarkTaskRequest extends FormRequest
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
            'task_id' => 'required|exists:tasks,id',
            'mark' => 'required|boolean'
        ];
    }

    public function messages()
    {
        // there are some custom error messages (it's optional)
        return [
            'task_id.required' => 'The title is required.',
            'task_id.exists' => 'The task ID does not exists',
            'mark.required' => 'The mark is required.',
        ];
    }
}
