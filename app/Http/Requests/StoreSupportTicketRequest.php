<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupportTicketRequest extends FormRequest
{
    protected $errorBag = 'supportTicket';

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
            'research_reference' => ['nullable', 'string', 'max:80'],
            'category' => ['required', Rule::in(['Account', 'Research Submission', 'Documents', 'Workflow', 'Permissions', 'Analytics', 'Technical Issue', 'Other'])],
            'subject' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'priority' => ['required', Rule::in(['Low', 'Normal', 'High', 'Urgent'])],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ];
    }
}
