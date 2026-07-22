<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255', 'unique:academic_years,label'],
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