<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'frais_scolarite',
        'frais_dejeuner',
        'frais_activite',
        'is_active',
    ];

    protected $casts = [
        'frais_scolarite' => 'decimal:2',
        'frais_dejeuner' => 'decimal:2',
        'frais_activite' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'total_mensuel',
    ];

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function getTotalMensuelAttribute(): float
    {
        return (float) $this->frais_scolarite + (float) $this->frais_dejeuner + (float) $this->frais_activite;
    }
}