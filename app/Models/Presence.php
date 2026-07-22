<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'enfant_id',
        'date',
        'heure_arrivee',
        'heure_depart',
        'personne_depot',
        'personne_retrait',
        'remarque',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function enfant(): BelongsTo
    {
        return $this->belongsTo(Enfant::class);
    }
}
