<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreTaskFileUploadRequest extends FormRequest
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
            'attachments' => 'required|array|min:1|max:3',
            'attachments.*' => 'file|mimes:svg,png,jpg,jpeg,mp4,csv,txt,doc,docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:10240',
        ];
    }

    public function messages()
    {
        return [
            'task_id.required' => 'The task ID is required.',
            'task_id.exists' => 'The task ID does not exists',
            'attachments.required' => 'You must upload at least one file.',
            'attachments.*.mimes' => 'Invalid file format. Allowed formats are SVG, PNG, JPG, MP4, CSV, TXT, DOC, DOCX.',
            'attachments.*.max' => 'Each file may not be greater than 10MB.',
        ];
    }
}
