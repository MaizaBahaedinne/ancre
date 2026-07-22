<?php

namespace App\Http\Requests;

use App\Models\Incident;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentRequest extends FormRequest
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
            'date' => ['required', 'date', 'before_or_equal:today'],
            'type_incident' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'action_realisee' => ['nullable', 'string'],
            'workflow_status' => ['nullable', Rule::in(array_keys(Incident::WORKFLOW_OPTIONS))],
            'notify_parent' => ['nullable', 'boolean'],
            'responsable_personnel_id' => ['nullable', 'exists:personnels,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'workflow_status' => $this->input('workflow_status', Incident::WORKFLOW_OPEN),
            'notify_parent' => $this->boolean('notify_parent'),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('workflow_status') === Incident::WORKFLOW_TAKEN && ! $this->filled('responsable_personnel_id')) {
                $validator->errors()->add('responsable_personnel_id', 'Attribuez un personnel pour passer l\'incident en prise en charge.');
            }
        });
    }
}
