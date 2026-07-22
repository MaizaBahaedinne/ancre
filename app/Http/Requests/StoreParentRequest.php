<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreParentRequest extends FormRequest
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
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'numero_cin' => ['required', 'string', 'max:20', 'unique:parents,numero_cin'],
            'date_delivrance_cin' => ['required', 'date'],
            'date_naissance' => ['required', 'date', 'before_or_equal:today'],
            'sexe' => ['required', 'in:M,F'],
            'telephone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255', 'unique:parents,email'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'contact_urgence' => ['nullable', 'string', 'max:30'],
            'cin_recto' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'cin_verso' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ];
    }
}
