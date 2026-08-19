<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitrineSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'tagline',
        'hero_title',
        'hero_subtitle',
        'address',
        'phone',
        'email',
        'parent_space_url',
        'map_embed_url',
        'facebook_url',
        'instagram_url',
        'tiktok_url',
        'youtube_url',
        'countdown_enabled',
        'countdown_target_at',
        'countdown_timezone',
        'countdown_title',
        'countdown_subtitle',
        'countdown_expired_label',
    ];

    protected $casts = [
        'countdown_enabled' => 'boolean',
        'countdown_target_at' => 'datetime',
    ];
}
