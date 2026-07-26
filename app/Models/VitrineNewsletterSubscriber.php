<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitrineNewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'source_page',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
