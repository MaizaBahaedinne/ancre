<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VitrineBlogPostReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'vitrine_blog_post_id',
        'user_id',
        'reaction',
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
