<?php

namespace App\Http\Requests;

use App\Models\Activite;
use App\Models\Salle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateActiviteRequest extends FormRequest
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
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'heure_debut' => ['required', 'date_format:H:i'],
            'heure_fin' => ['required', 'date_format:H:i', 'after:heure_debut'],
            'recurrence' => ['nullable', Rule::in(['journalier', 'hebdomadaire', 'mensuelle', 'trimestrielle', 'semestrielle', 'annuelle'])],
            'recurrence_jours' => ['nullable', 'array'],
            'recurrence_jours.*' => ['nullable', Rule::in(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'])],
            'recurrence_jour_mois' => ['nullable', 'integer', 'min:1', 'max:31'],
            'recurrence_date_annuelle' => ['nullable', 'date', 'after_or_equal:date'],
            'date_fin_recurrence' => ['nullable', 'date', 'after:today', 'after_or_equal:date'],
            'responsable_personnel_id' => ['required', 'exists:personnels,id'],
            'salle_id' => ['required', 'exists:salles,id'],
            'responsable' => ['nullable', 'string', 'max:255'],
            'capacite' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'frais_participation' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'heure_debut' => $this->normalizeTime($this->input('heure_debut')),
            'heure_fin' => $this->normalizeTime($this->input('heure_fin')),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $recurrence = $this->input('recurrence');

            if (!empty($recurrence) && !$this->filled('date_fin_recurrence')) {
                $validator->errors()->add('date_fin_recurrence', 'La date de fin de recurrence est obligatoire.');
            }

            if ($recurrence === 'hebdomadaire' && empty($this->input('recurrence_jours', []))) {
                $validator->errors()->add('recurrence_jours', 'Selectionnez au moins un jour pour la recurrence hebdomadaire.');
            }

            if (in_array($recurrence, ['mensuelle', 'trimestrielle', 'semestrielle'], true) && !$this->filled('recurrence_jour_mois')) {
                $validator->errors()->add('recurrence_jour_mois', 'Selectionnez le jour du mois pour cette recurrence.');
            }

            if ($recurrence === 'annuelle' && !$this->filled('recurrence_date_annuelle')) {
                $validator->errors()->add('recurrence_date_annuelle', 'La date exacte de recurrence annuelle est obligatoire.');
            }

            if ($this->filled('salle_id')) {
                $salle = Salle::query()->find($this->integer('salle_id'));

                if ($salle && $salle->statut !== Salle::STATUT_DISPONIBLE) {
                    $validator->errors()->add('salle_id', 'La salle selectionnee n\'est pas disponible.');
                }
            }

            if ($this->filled('salle_id') && $this->filled('date') && $this->filled('heure_debut') && $this->filled('heure_fin')) {
                $currentActivity = $this->route('activite');

                $conflictExists = Activite::query()
                    ->where('salle_id', $this->integer('salle_id'))
                    ->whereDate('date', $this->input('date'))
                    ->when($currentActivity, fn ($query) => $query->whereKeyNot($currentActivity->id))
                    ->whereRaw('COALESCE(heure_debut, heure) < ?', [$this->input('heure_fin')])
                    ->whereRaw('COALESCE(heure_fin, heure_debut, heure) > ?', [$this->input('heure_debut')])
                    ->exists();

                if ($conflictExists) {
                    $validator->errors()->add('salle_id', 'Cette salle est deja reservee sur ce creneau horaire.');
                }
            }

            if ($this->filled('responsable_personnel_id') && $this->filled('date') && $this->filled('heure_debut') && $this->filled('heure_fin')) {
                $currentActivity = $this->route('activite');

                $responsableConflictExists = Activite::query()
                    ->where('responsable_personnel_id', $this->integer('responsable_personnel_id'))
                    ->whereDate('date', $this->input('date'))
                    ->when($currentActivity, fn ($query) => $query->whereKeyNot($currentActivity->id))
                    ->whereRaw('COALESCE(heure_debut, heure) < ?', [$this->input('heure_fin')])
                    ->whereRaw('COALESCE(heure_fin, heure_debut, heure) > ?', [$this->input('heure_debut')])
                    ->exists();

                if ($responsableConflictExists) {
                    $validator->errors()->add('responsable_personnel_id', 'Ce responsable a deja une autre activite sur ce creneau horaire.');
                }
            }
        });
    }

    private function normalizeTime(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value) === 1) {
            return substr($value, 0, 5);
        }

        return $value;
    }
}
