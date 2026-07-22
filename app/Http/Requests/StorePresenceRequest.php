<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePresenceRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'heure_arrivee' => ['nullable', 'date_format:H:i'],
            'heure_depart' => ['nullable', 'date_format:H:i'],
            'personne_depot' => ['nullable', 'string', 'max:255'],
            'personne_retrait' => ['nullable', 'string', 'max:255'],
            'remarque' => ['nullable', 'string'],
        ];
    }
}
