<?php

namespace App\Http\Requests;

use App\Models\PersonnelReferenceOption;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonnelRequest extends FormRequest
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
        $personnelId = $this->route('personnel')?->id;

        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'sexe' => ['required', 'in:M,F'],
            'date_naissance' => ['required', 'date', 'before:today'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'school_id' => ['nullable', 'exists:schools,id'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'fonction' => [
                'required',
                'string',
                Rule::exists('personnel_reference_options', 'label')->where(fn ($query) => $query->where('type', PersonnelReferenceOption::TYPE_FONCTION)->where('is_active', true)),
            ],
            'departement' => [
                'required',
                'string',
                Rule::exists('personnel_reference_options', 'label')->where(fn ($query) => $query->where('type', PersonnelReferenceOption::TYPE_DEPARTEMENT)->where('is_active', true)),
            ],
            'niveau_etude' => [
                'required',
                'string',
                Rule::exists('personnel_reference_options', 'label')->where(fn ($query) => $query->where('type', PersonnelReferenceOption::TYPE_NIVEAU_ETUDE)->where('is_active', true)),
            ],
            'domaine_etude' => ['nullable', 'string', 'max:255'],
            'annees_experience' => ['required', 'integer', 'min:0', 'max:80'],
            'numero_cin' => ['required', 'string', 'max:50', Rule::unique('personnels', 'numero_cin')->ignore($personnelId)],
            'date_delivrance_cin' => ['required', 'date', 'before_or_equal:today'],
            'lieu_delivrance_cin' => ['required', 'string', 'max:255'],
            'adresse_rue' => ['nullable', 'string', 'max:255'],
            'adresse_ville' => ['nullable', 'string', 'max:255'],
            'adresse_gouvernorat' => ['nullable', 'string', 'max:255'],
            'adresse_code_postal' => ['nullable', 'string', 'max:20'],
            'telephone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('personnels', 'email')->ignore($personnelId)],
            'numero_cnss' => ['nullable', 'string', 'max:50'],
            'date_embauche' => ['required', 'date', 'before_or_equal:today'],
            'manager_id' => ['nullable', 'exists:personnels,id', Rule::notIn([$personnelId])],
            'user_role' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $personnel = $this->route('personnel');
        $linkedUserId = $personnel?->user_id;

        $validator->sometimes('email', [
            'required',
            Rule::unique('users', 'email')->ignore($linkedUserId),
        ], function ($input) {
            return filled($input->user_role);
        });

        $validator->sometimes('user_role', ['exists:roles,name'], function ($input) {
            return filled($input->user_role);
        });
    }
}
