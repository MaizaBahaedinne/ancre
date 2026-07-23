<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:150', 'unique:packages,nom'],
            'include_scolarite' => ['required', 'boolean'],
            'include_dejeuner' => ['required', 'boolean'],
            'include_activite' => ['required', 'boolean'],
            'frais_scolarite' => ['nullable', 'numeric', 'min:0', 'required_if:include_scolarite,1'],
            'frais_dejeuner' => ['nullable', 'numeric', 'min:0', 'required_if:include_dejeuner,1'],
            'frais_activite' => ['nullable', 'numeric', 'min:0', 'required_if:include_activite,1'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'include_scolarite' => $this->boolean('include_scolarite'),
            'include_dejeuner' => $this->boolean('include_dejeuner'),
            'include_activite' => $this->boolean('include_activite'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function packageData(): array
    {
        $validated = $this->validated();

        return [
            'nom' => $validated['nom'],
            'frais_scolarite' => $validated['include_scolarite'] ? (float) ($validated['frais_scolarite'] ?? 0) : 0,
            'frais_dejeuner' => $validated['include_dejeuner'] ? (float) ($validated['frais_dejeuner'] ?? 0) : 0,
            'frais_activite' => $validated['include_activite'] ? (float) ($validated['frais_activite'] ?? 0) : 0,
            'is_active' => $validated['is_active'],
        ];
    }
}