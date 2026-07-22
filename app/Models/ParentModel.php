<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = [
        'nom',
        'prenom',
        'numero_cin',
        'date_delivrance_cin',
        'date_naissance',
        'sexe',
        'telephone',
        'email',
        'adresse',
        'profession',
        'contact_urgence',
        'user_id',
        'cin_recto',
        'cin_verso',
    ];

    protected $casts = [
        'date_delivrance_cin' => 'date',
        'date_naissance' => 'date',
    ];

    public function enfants(): HasMany
    {
        return $this->hasMany(Enfant::class, 'parent_id');
    }

    public function enfantRelations(): HasMany
    {
        return $this->hasMany(EnfantParentRelation::class, 'parent_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activityRegistrations(): HasMany
    {
        return $this->hasMany(ActivityRegistration::class, 'parent_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(ParentRequest::class, 'parent_id');
    }
}
