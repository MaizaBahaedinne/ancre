<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitrineService extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'icon',
        'thumbnail',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
