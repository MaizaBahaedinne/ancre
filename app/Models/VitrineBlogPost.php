<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitrineBlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'cover_url',
        'excerpt',
        'content',
        'sort_order',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];
}
