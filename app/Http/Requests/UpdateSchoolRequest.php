<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $schoolId = $this->route('school')?->id;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('schools', 'name')->ignore($schoolId)],
            'address_route' => ['nullable', 'string', 'max:255'],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_postal_code' => ['nullable', 'string', 'max:20'],
            'address_city' => ['nullable', 'string', 'max:255'],
            'address_governorate' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'director_name' => ['nullable', 'string', 'max:255'],
            'director_contact' => ['nullable', 'string', 'max:255'],
            'classes' => ['nullable', 'array'],
            'classes.*.academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'classes.*.name' => ['nullable', 'string', 'max:255'],
            'classes.*.level' => ['nullable', 'string', 'max:255'],
            'classes.*.capacity' => ['nullable', 'integer', 'min:1', 'max:500'],
            'classes.*.is_active' => ['nullable', 'boolean'],
        ];
    }
}