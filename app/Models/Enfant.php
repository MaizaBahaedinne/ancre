<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Enfant extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'school_class_id',
        'nom',
        'prenom',
        'date_naissance',
        'sexe',
        'classe',
        'photo',
        'has_allergie',
        'allergie_options',
        'allergies',
        'observations',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'has_allergie' => 'boolean',
        'allergie_options' => 'array',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function familyRelations(): HasMany
    {
        return $this->hasMany(EnfantParentRelation::class);
    }

    public function activityParticipations(): HasMany
    {
        return $this->hasMany(EnfantActivite::class);
    }

    public function activityRegistrations(): HasMany
    {
        return $this->hasMany(ActivityRegistration::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(ParentRequest::class);
    }
}
