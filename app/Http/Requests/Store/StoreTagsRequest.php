<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreTagsRequest extends FormRequest
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
            'tags' => 'required|array'
        ];
    }

    public function messages()
    {
        // there are some custom error messages (it's optional)
        return [
            'task_id.required' => 'The title is required.',
            'task_id.exists' => 'The task ID does not exists',
            'tags.required' => 'The tags is required.',
            'tags.array' => 'The tags must be an array',
        ];
    }
}
