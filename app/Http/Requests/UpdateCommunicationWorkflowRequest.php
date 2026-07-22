<?php

namespace App\Http\Requests;

use App\Models\ParentRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCommunicationWorkflowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workflow_status' => ['required', Rule::in(array_keys(ParentRequest::STATUS_OPTIONS))],
            'resolution_note' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $status = $this->input('workflow_status');

            if (in_array($status, [ParentRequest::STATUS_PROCESSED, ParentRequest::STATUS_REJECTED], true) && ! $this->filled('resolution_note')) {
                $validator->errors()->add('resolution_note', 'Ajoutez une note de resolution pour finaliser le dossier.');
            }
        });
    }
}