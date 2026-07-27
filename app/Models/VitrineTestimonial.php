<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitrineTestimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_name',
        'child_name',
        'parent_photo_url',
        'content',
        'rating',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'rating' => 'integer',
        'sort_order' => 'integer',
        'is_published' => 'boolean',
    ];
}
