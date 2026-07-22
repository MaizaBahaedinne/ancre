<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salle extends Model
{
    use HasFactory;

    public const STATUT_DISPONIBLE = 'disponible';

    public const STATUT_RESERVEE = 'reservee';

    public const STATUT_MAINTENANCE = 'maintenance';

    public const STATUT_INDISPONIBLE = 'indisponible';

    public const STATUT_OPTIONS = [
        self::STATUT_DISPONIBLE => 'Disponible',
        self::STATUT_RESERVEE => 'Reservee',
        self::STATUT_MAINTENANCE => 'Maintenance',
        self::STATUT_INDISPONIBLE => 'Indisponible',
    ];

    public const EQUIPEMENT_OPTIONS = [
        'tableau' => 'Tableau',
        'datashow' => 'Datashow',
        'ecran' => 'Ecran',
        'sonorisation' => 'Sonorisation',
        'climatisation' => 'Climatisation',
    ];

    protected $fillable = [
        'nom',
        'etage',
        'capacite',
        'equipements',
        'statut',
        'responsable_personnel_id',
    ];

    protected $casts = [
        'capacite' => 'integer',
        'equipements' => 'array',
    ];

    public function responsablePersonnel(): BelongsTo
    {
        return $this->belongsTo(Personnel::class, 'responsable_personnel_id');
    }

    public function activites(): HasMany
    {
        return $this->hasMany(Activite::class);
    }
}
