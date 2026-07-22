<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $academicYearId = $this->route('academic_year')?->id;

        return [
            'label' => ['required', 'string', 'max:255', Rule::unique('academic_years', 'label')->ignore($academicYearId)],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'registration_fee' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'periods' => ['nullable', 'array'],
            'periods.*.title' => ['required_with:periods.*.type', 'nullable', 'string', 'max:255'],
            'periods.*.type' => ['nullable', Rule::in(array_keys(\App\Models\AcademicCalendarPeriod::TYPE_OPTIONS))],
            'periods.*.start_date' => ['nullable', 'date'],
            'periods.*.end_date' => ['nullable', 'date', 'after_or_equal:periods.*.start_date'],
            'periods.*.notes' => ['nullable', 'string'],
        ];
    }
}