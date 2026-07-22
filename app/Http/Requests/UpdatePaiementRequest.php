<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaiementRequest extends FormRequest
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
            'montant' => ['required', 'numeric', 'min:0'],
            'date_paiement' => ['required', 'date'],
            'mois' => ['required', 'integer', 'between:1,12'],
            'annee' => ['required', 'integer', 'min:2000', 'max:2100'],
            'mode_paiement' => ['required', 'in:Especes,Carte,Virement,Cheque'],
            'statut' => ['required', 'in:Paye,En retard,Partiel'],
            'commentaire' => ['nullable', 'string'],
        ];
    }
}
