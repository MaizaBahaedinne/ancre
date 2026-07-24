<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnfantEvaluationGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'enfant_evaluation_id',
        'academic_subject_id',
        'grade',
        'coefficient',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
        'coefficient' => 'decimal:2',
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(EnfantEvaluation::class, 'enfant_evaluation_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class, 'academic_subject_id');
    }
}
