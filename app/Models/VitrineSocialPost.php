<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitrineSocialPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'post_url',
        'thumbnail_url',
        'thumbnail_path',
        'caption',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
