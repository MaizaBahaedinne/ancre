<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnfantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'parent_id' => ['required', 'exists:parents,id'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'date_naissance' => ['required', 'date', 'before_or_equal:today'],
            'sexe' => ['required', 'in:M,F'],
            'classe' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'has_allergie' => ['nullable', 'boolean'],
            'allergie_options' => ['nullable', 'array'],
            'allergie_options.*' => ['string', 'max:255'],
            'allergies' => ['nullable', 'string', 'required_if:has_allergie,1'],
            'observations' => ['nullable', 'string'],
            'relations' => ['nullable', 'array'],
            'relations.*' => ['nullable', 'distinct', 'exists:parents,id'],
        ];
    }
}
