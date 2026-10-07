<?php

namespace App\Http\Requests;

use App\Models\Thesis;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreThesisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Thesis::class) === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'abstract' => ['nullable', 'string', 'max:10000'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'program' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'academic_year' => ['required', 'string', 'max:20'],
            'school_year' => ['nullable', 'string', 'max:20'],
            'adviser_id' => ['nullable', 'integer', 'exists:users,id'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}
