<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTriggerOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'trigger',
        'name',
        'description',
        'module',
        'is_enabled',
        'receivers',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'receivers' => 'array',
    ];
}
