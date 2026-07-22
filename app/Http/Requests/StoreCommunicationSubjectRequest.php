<?php

namespace App\Http\Requests;

use App\Models\ParentRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommunicationSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action_type' => ['required', Rule::in(array_keys(ParentRequest::ACTION_OPTIONS))],
            'label' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}