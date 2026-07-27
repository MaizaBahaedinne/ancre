<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function comments(): HasMany
    {
        return $this->hasMany(VitrineBlogPostComment::class, 'vitrine_blog_post_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(VitrineBlogPostReaction::class, 'vitrine_blog_post_id');
    }
}
