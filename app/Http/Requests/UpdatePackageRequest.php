<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => [
                'required',
                'string',
                'max:150',
                Rule::unique('packages', 'nom')->ignore($this->route('package')),
            ],
            'frais_scolarite' => ['required', 'numeric', 'min:0'],
            'frais_dejeuner' => ['required', 'numeric', 'min:0'],
            'frais_activite' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}