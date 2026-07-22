<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'sexe',
        'date_naissance',
        'photo',
        'school_id',
        'school_class_id',
        'fonction',
        'departement',
        'niveau_etude',
        'domaine_etude',
        'annees_experience',
        'numero_cin',
        'date_delivrance_cin',
        'lieu_delivrance_cin',
        'adresse_rue',
        'adresse_ville',
        'adresse_gouvernorat',
        'adresse_code_postal',
        'telephone',
        'email',
        'numero_cnss',
        'date_embauche',
        'manager_id',
        'user_id',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_embauche' => 'date',
        'date_delivrance_cin' => 'date',
        'annees_experience' => 'integer',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(self::class, 'manager_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }
}
