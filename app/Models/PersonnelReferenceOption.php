<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonnelReferenceOption extends Model
{
    use HasFactory;

    public const TYPE_FONCTION = 'fonction';
    public const TYPE_DEPARTEMENT = 'departement';
    public const TYPE_NIVEAU_ETUDE = 'niveau_etude';

    protected $fillable = [
        'type',
        'label',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}