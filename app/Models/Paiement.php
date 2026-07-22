<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'enfant_id',
        'montant',
        'date_paiement',
        'mois',
        'annee',
        'mode_paiement',
        'statut',
        'commentaire',
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant' => 'decimal:2',
    ];

    public function enfant(): BelongsTo
    {
        return $this->belongsTo(Enfant::class);
    }
}
