<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VitrineTestimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('created_at')->orderBy('sort_order');
    }
}
