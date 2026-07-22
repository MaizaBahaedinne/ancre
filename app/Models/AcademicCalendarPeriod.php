<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicCalendarPeriod extends Model
{
    use HasFactory;

    public const TYPE_THEORETICAL_EXAM = 'theoretical_exam';
    public const TYPE_PRACTICAL_EXAM = 'practical_exam';
    public const TYPE_SYNTHESIS_EXAM = 'synthesis_exam';
    public const TYPE_SCHOOL_VACATION = 'school_vacation';
    public const TYPE_PUBLIC_HOLIDAY = 'public_holiday';

    public const TYPE_OPTIONS = [
        self::TYPE_THEORETICAL_EXAM => 'Examen theorique',
        self::TYPE_PRACTICAL_EXAM => 'Examen pratique',
        self::TYPE_SYNTHESIS_EXAM => 'Examen synthese',
        self::TYPE_SCHOOL_VACATION => 'Vacances scolaires',
        self::TYPE_PUBLIC_HOLIDAY => 'Jour ferie',
    ];

    protected $fillable = [
        'academic_year_id',
        'title',
        'type',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}