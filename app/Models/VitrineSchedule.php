<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitrineSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_label',
        'open_at',
        'close_at',
        'is_closed',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'is_active' => 'boolean',
    ];
}
