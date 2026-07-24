<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EnfantEvaluation extends Model
{
    use HasFactory;

    public const TRIMESTER_OPTIONS = ['Trimestre 1', 'Trimestre 2', 'Trimestre 3'];

    protected $fillable = [
        'enfant_id',
        'academic_year_id',
        'school_class_id',
        'trimester',
        'general_average',
        'class_rank',
        'bulletin_received_at',
        'notes',
    ];

    protected $casts = [
        'general_average' => 'decimal:2',
        'class_rank' => 'integer',
        'bulletin_received_at' => 'date',
    ];

    public function enfant(): BelongsTo
    {
        return $this->belongsTo(Enfant::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(EnfantEvaluationGrade::class);
    }
}
