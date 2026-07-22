<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInscriptionRequest extends FormRequest
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
            'enfant_id' => ['required', 'exists:enfants,id'],
            'package_id' => ['required', 'exists:packages,id'],
            'annee_scolaire' => ['required', 'string', 'max:20'],
            'date_inscription' => ['required', 'date'],
            'type_garde' => ['required', 'in:Matin,Apres-midi,Journee complete'],
            'statut' => ['required', 'in:Active,Renouvelee,Suspendue,Annulee'],
        ];
    }
}
