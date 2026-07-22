<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class EnfantActivite extends Model
{
    use HasFactory;

    protected $fillable = [
        'enfant_id',
        'activite_id',
        'statut',
        'remarque',
    ];

    public function enfant(): BelongsTo
    {
        return $this->belongsTo(Enfant::class);
    }

    public function activite(): BelongsTo
    {
        return $this->belongsTo(Activite::class);
    }
}
