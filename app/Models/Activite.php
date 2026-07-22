<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Activite extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'date',
        'heure',
        'heure_debut',
        'heure_fin',
        'recurrence',
        'recurrence_jours',
        'recurrence_jour_mois',
        'recurrence_date_annuelle',
        'date_fin_recurrence',
        'responsable_personnel_id',
        'salle_id',
        'responsable',
        'capacite',
        'frais_participation',
    ];

    protected $casts = [
        'date' => 'date',
        'recurrence_jours' => 'array',
        'recurrence_date_annuelle' => 'date',
        'date_fin_recurrence' => 'date',
        'capacite' => 'integer',
        'frais_participation' => 'decimal:2',
    ];

    public function responsablePersonnel(): BelongsTo
    {
        return $this->belongsTo(Personnel::class, 'responsable_personnel_id');
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class);
    }

    public function participations(): HasMany
    {
        return $this->hasMany(EnfantActivite::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(ActivityRegistration::class);
    }

    public function startsAt(): ?Carbon
    {
        if (! $this->date) {
            return null;
        }

        $startAt = $this->date->copy();
        $startTime = $this->heure_debut ?: $this->heure;

        if (! $startTime) {
            return $startAt->startOfDay();
        }

        return $startAt->setTimeFromTimeString($this->normalizeTimeString($startTime));
    }

    public function endsAt(): ?Carbon
    {
        $startAt = $this->startsAt();

        if (! $startAt) {
            return null;
        }

        if (! $this->heure_fin) {
            return $startAt->copy()->addHour();
        }

        return $this->date->copy()->setTimeFromTimeString($this->normalizeTimeString($this->heure_fin));
    }

    public function participationCutoffAt(): ?Carbon
    {
        $endAt = $this->endsAt();

        return $endAt?->copy()->addHour();
    }

    private function normalizeTimeString(string $time): string
    {
        return strlen($time) === 5 ? $time.':00' : $time;
    }
}
