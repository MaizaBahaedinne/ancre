<?php

namespace App\Http\Requests;

use App\Models\ParentRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommunicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'enfant_id' => ['required', 'exists:enfants,id'],
            'action_type' => ['required', Rule::in(array_keys(ParentRequest::ACTION_OPTIONS))],
            'subject_id' => ['nullable', 'exists:parent_request_subjects,id'],
            'subject_other' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx', 'max:5120'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('subject_id') && ! $this->filled('subject_other')) {
                $validator->errors()->add('subject_other', 'Selectionnez un sujet ou saisissez un sujet libre.');
            }
        });
    }
}