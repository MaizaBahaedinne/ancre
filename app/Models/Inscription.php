<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'enfant_id',
        'package_id',
        'annual_registration_fee',
        'package_monthly_total',
        'total_amount',
        'annee_scolaire',
        'date_inscription',
        'type_garde',
        'statut',
    ];

    protected $casts = [
        'date_inscription' => 'date',
        'annual_registration_fee' => 'decimal:2',
        'package_monthly_total' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function enfant(): BelongsTo
    {
        return $this->belongsTo(Enfant::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function getResolvedAnnualRegistrationFeeAttribute(): float
    {
        return (float) ($this->annual_registration_fee ?? 0);
    }

    public function getResolvedPackageMonthlyTotalAttribute(): float
    {
        if ((float) ($this->package_monthly_total ?? 0) > 0) {
            return (float) $this->package_monthly_total;
        }

        return (float) ($this->package?->total_mensuel ?? 0);
    }

    public function getResolvedTotalAmountAttribute(): float
    {
        if ((float) ($this->total_amount ?? 0) > 0) {
            return (float) $this->total_amount;
        }

        return $this->resolved_package_monthly_total + $this->resolved_annual_registration_fee;
    }
}
