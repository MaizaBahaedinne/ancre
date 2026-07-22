<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class EnfantParentRelation extends Model
{
    use HasFactory;

    protected $fillable = [
        'enfant_id',
        'parent_id',
        'relation',
    ];

    public function enfant(): BelongsTo
    {
        return $this->belongsTo(Enfant::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }
}
