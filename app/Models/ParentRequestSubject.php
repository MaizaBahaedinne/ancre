<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentRequestSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_type',
        'label',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function requests(): HasMany
    {
        return $this->hasMany(ParentRequest::class, 'subject_id');
    }
}