<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VitrineBlogPostComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'vitrine_blog_post_id',
        'user_id',
        'content',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function blogPost(): BelongsTo
    {
        return $this->belongsTo(VitrineBlogPost::class, 'vitrine_blog_post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
