<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address_route',
        'address_street',
        'address_postal_code',
        'address_city',
        'address_governorate',
        'city',
        'phone',
        'director_name',
        'director_contact',
    ];

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }
}