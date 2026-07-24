<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicSubject extends Model
{
    use HasFactory;

    public const LEVEL_OPTIONS = [
        '1ère année',
        '2ème année',
        '3ème année',
        '4ème année',
        '5ème année',
        '6ème année',
        '7ème année',
        '8ème année',
        '9ème année',
    ];

    protected $fillable = [
        'name',
        'level',
        'default_coefficient',
        'is_active',
    ];

    protected $casts = [
        'default_coefficient' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(EnfantEvaluationGrade::class);
    }
}
