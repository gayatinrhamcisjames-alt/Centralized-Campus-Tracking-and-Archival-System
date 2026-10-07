<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIssueReportRequest extends FormRequest
{
    protected $errorBag = 'issueReport';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category' => ['required', Rule::in(['Account', 'Research Submission', 'Documents', 'Workflow', 'Permissions', 'Analytics', 'Technical Issue', 'Other'])],
            'page_url' => ['nullable', 'url', 'max:2048'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'reproduction_steps' => ['nullable', 'string', 'max:5000'],
            'browser' => ['nullable', 'string', 'max:120'],
            'device' => ['nullable', 'string', 'max:120'],
            'severity' => ['required', Rule::in(['Minor', 'Moderate', 'Major', 'Critical'])],
            'screenshot' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
