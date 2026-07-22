<?php

namespace App\Http\Requests;

use App\Models\Salle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSalleRequest extends FormRequest
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
        $salleId = $this->route('salle')?->id;

        return [
            'nom' => ['required', 'string', 'max:120', Rule::unique('salles', 'nom')->ignore($salleId)],
            'etage' => ['required', 'string', 'max:50'],
            'capacite' => ['required', 'integer', 'min:1', 'max:1000'],
            'equipements' => ['nullable', 'array'],
            'equipements.*' => ['nullable', Rule::in(array_keys(Salle::EQUIPEMENT_OPTIONS))],
            'statut' => ['required', Rule::in(array_keys(Salle::STATUT_OPTIONS))],
            'responsable_personnel_id' => ['nullable', 'exists:personnels,id'],
        ];
    }
}
